<?php

class LanguagesInstalled extends AppModel
{
    public $table = 'languages_installed';

    public function Language()
    {
        return $this->belongsTo('Language');
    }

    public function Addon()
    {
        return $this->belongsTo('Addon');
    }

}
