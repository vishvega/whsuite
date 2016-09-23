<?php

class Language extends AppModel
{
    protected $fillable = array(
        'name', 'slug', 'is_active', 'is_default',
        'decimal_point', 'thousand_separator',
        'language_code', 'text_direction', 'date_format',
        'time_format'
    );


    public function LanguagePhrase()
    {
        return $this->hasMany('LanguagePhrase');
    }

    public static function active()
    {
        return self::where('is_active', '=', '1')->get();
    }

}
