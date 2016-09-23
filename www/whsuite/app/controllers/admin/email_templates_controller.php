<?php

use \Illuminate\Support\Str;
use \Whsuite\Inputs\Post as PostInput;

class EmailTemplatesController extends AdminController
{
    /**
     * scaffolding overrides for group listing
     *
     * see admin base controller for doc blocks
     */
    protected function indexToolbar()
    {
        return array(
            array(
                'url_route' => 'admin-emailtemplate',
                'link_class' => '',
                'icon' => 'fa fa-user',
                'label' => 'emailtemplate_management'
            ),
            array(
                'url_route' => 'admin-emailtemplate-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'emailtemplate_add'
            )
        );
    }

    protected function indexColumns()
    {
        return array(
            array(
                'field' => 'name'
            ),
            array(
                'field' => 'slug'
            ),
            array(
                'field' => 'updated_at'
            ),
            array(
                'action' => 'edit',
                'label' => null
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
            'edit' => array(
                'url_route' => 'admin-emailtemplate-edit',
                'link_class' => 'btn btn-primary btn-small pull-right',
                'icon' => 'fa fa-pencil',
                'label' => 'edit',
                'params' => array('id')
            )
        );
    }

    protected function formFields()
    {
        $fields = array(
            'EmailTemplate.id',
            'EmailTemplate.name',
            'EmailTemplate.slug',
            'EmailTemplate.cc',
            'EmailTemplate.bcc'
        );

        return $fields;
    }

    protected function getExtraData($model)
    {
        $language = new Language();
        $languages = $language->active();

        $this->view->set('languages', $languages);


        // sort out the translations into a format we can work with a bit better
        // language id is basically made into the key

        $email_template_transations = $model->EmailTemplateTranslation()->get();
        $email_template_transations = $email_template_transations->toArray();

        $language_data = array();

        if (isset($email_template_transations) && ! empty($email_template_transations)) {
            foreach ($email_template_transations as $translation) {
                if (isset($translation['language_id'])) {
                    $language_data[$translation['language_id']] = $translation;
                }
            }
        }

        // get any post data already set
        $existing_translations = PostInput::get('data.translations');
        if (is_array($existing_translations)) {
            $language_data = array_merge($existing_translations, $language_data);
        }

        // set to post data
        PostInput::set('data.translations', $language_data);
    }

    protected function afterSave(&$main_model)
    {
        // Manually set the last updated date
        $date = \Carbon\Carbon::now();

        $main_model->updated_at = $date;
        $main_model->save();

        // check input for translations
        $translations = PostInput::get('data.translations');

        foreach ($translations as $language_id => $translation) {
            // check if we are updating or creating a new translation

            if (isset($translation['id']) && ! empty($translation['id'])) {
                $emailTemplateTranslation = EmailTemplateTranslation::find($translation['id']);
            } else {
                $emailTemplateTranslation = new EmailTemplateTranslation();

                // set the main template id and the language id
                $emailTemplateTranslation->email_template_id = $main_model->id;
                $emailTemplateTranslation->language_id = $language_id;
            }

            // loop and set all the translation data
            foreach ($translation as $field => $value) {
                $emailTemplateTranslation->$field = $value;
            }

            $emailTemplateTranslation->save();
        }
    }

    public function form($id = null)
    {
        $this->render_view = 'email_templates/form.php';

        return parent::form($id);
    }

    public function delete($id)
    {
        // prefix - either admin or client
        $prefix = $this->getSection();

        $this->model = $model = $this->getModel();
        $item = $model::find($id);

        if ($item->is_system == '0') {
            EmailTemplateTranslation::where('email_template_id', '=', $item->id)->delete();
            if ($item->delete()) {
                // Delete template translations.

                App::get('session')->setFlash('success', $this->lang->get('scaffolding_delete_success'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('scaffolding_delete_error'));
            }

        } else {
            App::get('session')->setFlash('error', $this->lang->get('scaffolding_delete_notallowed'));
        }

        return $this->redirect($prefix . '-' . strtolower($model));
    }
}
