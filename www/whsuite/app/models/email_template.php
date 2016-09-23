<?php

class EmailTemplate extends AppModel
{

    public function EmailTemplateTranslation()
    {
        return $this->hasMany('EmailTemplateTranslation');
    }

    public function Addon()
    {
        return $this->belongsTo('Addon');
    }

    /**
     * get existing email templates
     * based on the language and addon id
     * return with the slug as key so we can check if it's already installed
     *
     * @param   int         Language ID
     * @param   int         Addon ID or 0 if main WHSUITE pack
     * @return  array       Array containing LanguagePhrase Objects
     */
    public static function getExisting($language_id, $addon_id = 0)
    {
        $templates = array();

        $emailTemplates = self::where('addon_id', '=', $addon_id)
            ->with(array(
                'EmailTemplateTranslation' => function($query) use ($language_id) {

                    $query->where('language_id', '=', $language_id);
                }
            ))
            ->get();

        foreach ($emailTemplates as $EmailTemplate) {

            $templates[$EmailTemplate->slug] = count($EmailTemplate->EmailTemplateTranslation);
        }

        return $templates;
    }

}
