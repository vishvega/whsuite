<?php

use \Whsuite\Inputs\Post as PostInput;
use \Whsuite\Inputs\Files as FilesInput;
use \App\Libraries\LanguageHelper as LanguageHelper;

class LanguagesController extends AdminController
{
    /**
     * language listing
     */
    public function listing()
    {
        // first get the model we will be dealing with
        $this->model = $model = $this->getModel();

        // get the sectin header language
        $page_title = $this->lang->get(strtolower($model) . '_management');

        // build the breadcrumb
        $this->indexBreadcrumb($model, $page_title);

        // get the installed languages / addons
        $data = LanguagesInstalled::with(
            array(
                'Language',
                'Addon'
            )
        )->get();

        // load the listings helper
        App::factory('\App\Libraries\ListingsHelper');

        if (\App::get('session')->hasFlash('emailWarning')) {
            $this->view->set(
                'emailWarning',
                \App::get('session')->getFlash('emailWarning')
            );
        }

        $this->view->set(array(
            'data' => $data,
            'title' => $page_title,
            'toolbar' => $this->indexToolbar(),
            'columns' => $this->indexColumns(),
            'actions' => $this->indexActions(),
            'addon_helper' => App::factory('\App\Libraries\AddonHelper')
        ));

        $this->view->display('languages/listing.php');
    }

    /**
     * delete the language pack
     *
     * Delete the reference and then remove language phrases and language (if no one phrases for that language exist)
     */
    public function deletePack($languageInstalledId)
    {
        $installed = LanguagesInstalled::find($languageInstalledId);

        // check to see if it's english to prevent deleting the main english packs
        if (LanguageHelper::isEnglish($installed)) {
            App::get('session')->setFlash('error', $this->lang->get('cant_delete_english'));
            return $this->redirect('admin-language');
        }

        // remove all phrases
        \LanguagePhrase::where('language_id', '=', $installed->language_id)
            ->where('addon_id', '=', $installed->addon_id)
            ->delete();

        // get the IDs of all / any email templates belonging to this pack so we can
        // remove the translations
        $EmailTemplateIds = \EmailTemplate::where('addon_id', '=', $installed->addon_id)
            ->lists('id');

        \EmailTemplateTranslation::whereIn('email_template_id', $EmailTemplateIds)
            ->where('language_id', '=', $installed->language_id)
            ->delete();

        // check if we have any phrases on this language, we can then delete to prevent users setting that language
        $phrasesCount = \LanguagePhrase::where('language_id', '=', $installed->language_id)
            ->count();

        $emailCount = \EmailTemplateTranslation::where('language_id', '=', $installed->language_id)
            ->count();

        if ($phrasesCount === 0 && $emailCount === 0) {
            Language::where('id', '=', $installed->language_id)->delete();
        }

        $installed->delete();

        // clear the cache so the cache json files will be upto date
        \App::get('translation')->purge();

        App::get('session')->setFlash('success', $this->lang->get('scaffolding_delete_success'));
        return $this->redirect('admin-language');
    }

    /**
     * import a language pack
     *
     */
    public function import()
    {
        // first get the model we will be dealing with
        $this->model = $model = $this->getModel();

        // check to make sure the uploader is turned on.
        $uploaderOn = \App::checkInstalledAddon('uploader');

        $data = PostInput::get('data');

        $fileData = FilesInput::get('data');

        if (
            ! empty($data) &&
            isset($_POST['data']) &&
            ! empty($_POST['data']) &&
            $data['ImportCsv']['import'] === 'import-lang' &&
            $uploaderOn
        ) {
            $validTypes = array(
                'application/json',
                'text/plain'
            );

            if (in_array($fileData['type']['Language']['0']['filename'], $validTypes)) {
                // Dirty hack for using uploader, just assign everything to english
                // only needed to upload it
                $Language = Language::find(1);

                // process any uploads
                $uploaded = \Addon\Uploader\Libraries\Process::uploads('Language', $Language);

                if (! empty($uploaded['0']) && file_exists($uploaded['0'])) {
                    $json = file_get_contents($uploaded['0']);
                    $langPack = json_decode($json);

                    // check it's a valid language pack (i.e. check it has fields we're expecting)
                    if (
                        isset($langPack->name) &&
                        isset($langPack->slug) &&
                        isset($langPack->language_code) &&
                        isset($langPack->text_direction) &&
                        isset($langPack->Strings) &&
                        isset($langPack->EmailTemplates)
                    ) {
                        $importer = new \App\Libraries\LanguageImporter($langPack);
                        $result = $importer->import();
                        $Language = $importer->language();

                        if (
                            is_object($Language) &&
                            $Language->id > 0 &&
                            $result
                        ) {
                            $addon_id = $importer->addonId();
                            $emailWarning = $importer->emailWarnings();

                            // Log that we installed this language / addon so we can
                            if (! LanguageHelper::isInstalled($Language->id, $addon_id)) {
                                LanguageHelper::logInstalled($Language->id, $addon_id);
                            }

                            // clean up the directory and deleting any uploaded json files
                            LanguageHelper::cleanUpInstall();

                            // clear the cache so the cache json files will be upto date
                            \App::get('translation')->purge();

                            \App::get('session')->setFlash(
                                'success',
                                $this->lang->get('pack_installed')
                            );

                            if (! empty($emailWarning)) {
                                \App::get('session')->setFlash('emailWarning', $emailWarning);
                            }

                            return $this->redirect('admin-language');
                        }
                    }

                    // if we got here, there was a problem during import
                    \App\Libraries\Message::set(
                        $this->lang->get('pack_failed'),
                        'fail'
                    );

                    // clean up the directory and deleting any uploaded json files
                    LanguageHelper::cleanUpInstall();

                } else {
                    \App\Libraries\Message::set(
                        $this->lang->get('pack_upload_fail'),
                        'fail'
                    );
                }
            } else {
                \App\Libraries\Message::set(
                    $this->lang->get('upload_not_json'),
                    'fail'
                );
            }
        }

        if (! $uploaderOn) {
            \App\Libraries\Message::set(
                $this->lang->get('uploader_not_active'),
                'fail'
            );
        }

        $this->formBreadcrumb($model, $page_title = $this->lang->get('import_language'));

        $this->view->set(
            array(
                'title' => $page_title,
                'toolbar' => $this->formToolbar(null),
                'uploaderOn' => $uploaderOn
            )
        );

        $this->view->display('languages/import.php');
    }


    protected function indexColumns()
    {
        return array(
            array(
                'field' => 'language'
            ),
            array(
                'field' => 'package'
            ),
            array(
                'action' => 'delete',
                'label' => null
            )
        );
    }

    protected function indexActions()
    {
        return array(
            'delete' => array(
                'url_route' => 'admin-language-delete',
                'link_class' => 'btn btn-danger btn-small pull-right',
                'icon' => 'fa fa-remove',
                'label' => 'delete',
                'params' => array('id')
            )
        );
    }

    protected function indexToolbar()
    {
        return array(
            array(
                'url_route' => 'admin-language',
                'link_class' => '',
                'icon' => 'fa fa-list-ul',
                'label' => 'language_management'
            ),
            array(
                'url_route' => 'admin-language-import',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'import_language'
            )
        );
    }
}
