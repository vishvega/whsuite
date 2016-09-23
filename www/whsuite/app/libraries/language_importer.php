<?php

namespace App\Libraries;

class LanguageImporter
{
    /**
     * json object from language pack
     *
     * @var JSON Object
     */
    protected $langPack = null;

    /**
     * The language we are installing
     *
     * @var Language Object
     */
    protected $Language = null;

    /**
     * The addon id we are installing
     *
     * @var Int
     */
    protected $addon_id = 0;

    /**
     * date now
     *
     * @var string
     */
    protected $now = null;

    /**
     * the array of languages / email templates to install
     *
     * @var array
     */
    protected $toInsert = array(
        'LanguagePhrase' => array(),
        'EmailTemplateTranslation' => array()
    );

    /**
     * the array of languages to update
     *
     * @var array
     */
    protected $toUpdate = array();

    /**
     * the array of email templates to warn about
     *
     * @var array
     */
    protected $toWarn = array();

    /**
     * add the language pack json object to an internal variable
     *
     * @param   object  JSON Language pack object
     */
    public function __construct($langPack)
    {
        $this->langPack = $langPack;
        $this->now = \Carbon\Carbon::now();
    }


    /**
     * start the import process
     *
     * @return bool
     */
    public function import()
    {
        if (empty($this->langPack)) {

            return false;
        }

        $this->getLanguage();
        $this->getAddon();

        if (! $this->prepareImport()) {

            return false;
        }

        return $this->doImport();
    }


    /**
     * Return the language object we previously found
     *
     * @return  Object|null
     */
    public function language()
    {
        return $this->Language;
    }


    /**
     * Return the addon id we are processing
     *
     * @return  Object|null
     */
    public function addonId()
    {
        return $this->addon_id;
    }


    /**
     * Return the email warning array
     *
     * @return  array
     */
    public function emailWarnings()
    {
        return $this->toWarn;
    }


    /**
     * get a language object for import process
     * Either return existing one or create and return if doesn't exist
     *
     */
    protected function getLanguage()
    {
        $Language = \Language::where('slug', '=', $this->langPack->slug)
            ->first();

        if (! is_object($Language) || intval($Language->id) === 0) {

            $Language = new \Language(get_object_vars($this->langPack));
            $Language->save();
        }

        $this->Language = $Language;
    }


    /**
     * get the addon we are dealing with
     * or leave as 0 for main WHSuite language pack
     *
     */
    protected function getAddon()
    {
        if (! empty($this->langPack->addon)) {

            $Addon = \Addon::where('directory', '=', $this->langPack->addon)
                ->first();

            if (! empty($Addon->id)) {

                $this->addon_id = intval($Addon->id);
            }
        }
    }

    /**
     * prepare the language pack for import by sorting out the strings
     *
     * @return  bool
     */
    protected function prepareImport()
    {
        if (! is_object($this->Language) || ! is_int($this->addon_id) || intval($this->Language->id) === 0) {

            return false;
        }

        $Strings = \LanguagePhrase::getExisting($this->Language->id, $this->addon_id);
        $Emails = \EmailTemplate::getExisting($this->Language->id, $this->addon_id);

        // Loop the standard language strings
        foreach ($this->langPack->Strings as $Phrase) {

            if (isset($Strings[$Phrase->slug])) {

                $Strings[$Phrase->slug]->text = $Phrase->text;
                $this->toUpdate[] = $Strings[$Phrase->slug];

                continue;
            }

            $this->toInsert['LanguagePhrase'][] = array(
                'language_id' => $this->Language->id,
                'slug' => $Phrase->slug,
                'text' => $Phrase->text,
                'addon_id' => $this->addon_id,
                'created_at' => $this->now,
                'updated_at' => $this->now
            );
        }

        if (! empty($this->langPack->EmailTemplates)) {

            foreach ($this->langPack->EmailTemplates as $EmailTemplate) {

                // check if the email templat eexists and we have translatios for it
                // in this language
                // if so we only want to warn the user about updates incase they have overridden
                if (
                    isset($Emails[$EmailTemplate->slug]) &&
                    intval($Emails[$EmailTemplate->slug]) > 0
                ) {

                    $this->toWarn[] = $EmailTemplate->name;
                    continue;

                } else {

                    // it possible exists but no translations for the given language
                    // Either retrieve existing row or add new one if this is a fresh version of template
                    if (
                        isset($Emails[$EmailTemplate->slug]) &&
                        intval($Emails[$EmailTemplate->slug]) === 0
                    ) {

                        $Template = \EmailTemplate::where('slug', '=', $EmailTemplate->slug)
                            ->first();
                    } else {

                        $Template = new \EmailTemplate;
                        $Template->name = $EmailTemplate->name;
                        $Template->slug = $EmailTemplate->slug;
                        $Template->available_tags = $EmailTemplate->available_tags;
                        $Template->addon_id = $this->addon_id;
                        $Template->save();
                    }
                }

                $this->toInsert['EmailTemplateTranslation'][] = array(
                    'email_template_id' => $Template->id,
                    'language_id' => $this->Language->id,
                    'subject' => $EmailTemplate->EmailTemplateTranslation->subject,
                    'html_body' => $EmailTemplate->EmailTemplateTranslation->html_body,
                    'html_body_default' => $EmailTemplate->EmailTemplateTranslation->html_body_default,
                    'plaintext_body' => $EmailTemplate->EmailTemplateTranslation->plaintext_body,
                    'plaintext_body_default' => $EmailTemplate->EmailTemplateTranslation->plaintext_body_default
                );
            }
        }

        return true;
    }


    /**
     * do the actual inserts / updates
     *
     * @return bool
     */
    protected function doImport()
    {
        $LanguagePhrases = true;
        if (! empty($this->toInsert['LanguagePhrase'])) {

            $LanguagePhrases = \LanguagePhrase::insert($this->toInsert['LanguagePhrase']);
        }

        if (! empty($this->toUpdate)) {

            // Update any strings that already exist in the database
            foreach ($this->toUpdate as $LanguagePhrase) {

                $LanguagePhrase->save();
            }
        }

        $EmailTemplates = true;
        if (! empty($this->toInsert['EmailTemplateTranslation'])) {

            $EmailTemplates = \EmailTemplateTranslation::insert($this->toInsert['EmailTemplateTranslation']);
        }

        return ($LanguagePhrases && $EmailTemplates);
    }

}
