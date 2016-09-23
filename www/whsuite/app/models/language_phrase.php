<?php

class LanguagePhrase extends AppModel
{
    public function Language()
    {
        return $this->belongsTo('Language');
    }

    /**
     * Get all existing language phrases for the given language
     * and addon (if specified)
     *
     * @param   int         Language ID
     * @param   int|null    Addon ID or null if main WHSUITE pack
     * @return  array       Array containing LanguagePhrase Objects
     */
    public static function getExisting($language_id, $addon_id)
    {
        $phrases = array();

        if (! is_null($addon_id) && intval($addon_id) > 0) {

            $query = self::where('addon_id', '=', $addon_id);
        } else {

            $query = self::where('addon_id', '=', 0);
        }

        $LanguagePhrases = $query->where('language_id', '=', $language_id)
            ->get();

        foreach ($LanguagePhrases as $LanguagePhrase) {

            $phrases[$LanguagePhrase->slug] = $LanguagePhrase;
        }

        return $phrases;
    }

}
