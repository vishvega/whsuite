<?php

namespace App\Libraries;

class LanguageHelper
{

    /**
     * adds a row to mark a language / addon as installed
     *
     * @param   int     Language id to log
     * @param   int     Addon id to log or 0
     * @return  bool
     */
    public static function logInstalled($language_id, $addon_id)
    {
        $LanguagesInstalled = new \LanguagesInstalled;
        $LanguagesInstalled->language_id = intval($language_id);
        $LanguagesInstalled->addon_id = intval($addon_id);
        return $LanguagesInstalled->save();
    }

    /**
     * clean up after installing language pack by emptying the Language directory
     *
     */
    public static function cleanUpInstall()
    {
        \Addon\Uploader\Libraries\Process::delete('Language', 1);
    }

    /**
     * check to see if a language / addon combo has been installed
     *
     * @param   int     Language ID to check
     * @param   int     addon id to check (0 for main pack)
     * @return  bool
     */
    public static function isInstalled($language_id, $addon_id)
    {
        return (bool) \LanguagesInstalled::where('language_id', '=', $language_id)
            ->where('addon_id', '=', $addon_id)
            ->count();
    }

    /**
     * check to see if the given installed language row is english
     *
     * @param   Object  LanguagesInstalled object
     * @return  bool
     */
    public static function isEnglish($LanguagesInstalled)
    {
        $validInstall = (intval($LanguagesInstalled->id) > 0);
        $validLanguage = (intval($LanguagesInstalled->language_id) === 1);

        return ($validInstall && $validLanguage);
    }

    /**
     * remove all the language phrases for a given addon_id
     *
     * @param   int   Addon ID to remove the phrases for
     * @return  bool
     */
    public static function uninstallLanguages($addon_id)
    {
        $addon_id = intval($addon_id);
        if (intval($addon_id) === 0) {
            return false;
        }

        $return = \LanguagePhrase::where('addon_id', '=', $addon_id)->delete();
        \LanguagesInstalled::where('addon_id', '=', $addon_id)->delete();

        // remove the email translations
        $EmailTemplates = \EmailTemplate::where('addon_id', '=', $addon_id)
            ->get();

        if (! empty($EmailTemplates)) {
            foreach ($EmailTemplates as $EmailTemplate) {
                $EmailTemplate->EmailTemplateTranslation()->delete();
                $EmailTemplate->delete();
            }
        }

        return $return;
    }

    /**
     * scan the given app language folder
     * and import the languages phrases
     *
     * @param   Object  Addon Object
     */
    public static function importAppLanguages()
    {
        $langDir = STORAGE_DIR . DS . 'languages';
        self::importLanguages($langDir);
    }

    /**
     * scan the given addon directory for languages folder and loop all files
     * and import the languages phrases
     *
     * @param   Object  Addon Object
     */
    public static function importAddonLanguages($addon)
    {
        $langDir = ADDON_DIR . DS . $addon->directory . DS . 'languages';
        self::importLanguages($langDir);
    }

    /**
     * scan the given addon directory for languages folder and loop all files
     * and import the languages phrases
     *
     * @param   string $langDir Path to the folder contain the language packs
     */
    protected static function importLanguages($langDir)
    {
        $validDir = is_dir($langDir);
        $packsExist = (count(glob($langDir . DS . '*')) > 0);

        if ($validDir && $packsExist) {
            $finder = new \Symfony\Component\Finder\Finder();
            $finder->files()->in($langDir);
            $finder->files()->name('*.json');

            foreach ($finder as $file) {
                $filePath = $langDir . DS . $file->getFileName();

                if (! is_readable($filePath)) {
                    continue;
                }

                $langPack = json_decode(file_get_contents($filePath));

                if (json_last_error() === JSON_ERROR_NONE) {
                    $importer = new \App\Libraries\LanguageImporter($langPack);
                    $result = $importer->import();
                    $Language = $importer->language();
                    $addon_id = $importer->addonId();

                    if ($result && ! self::isInstalled($Language->id, $addon_id)) {
                        self::logInstalled($Language->id, $addon_id);
                    }
                }
            }
        }
    }
}
