<?php

class Addon extends AppModel
{
    public function Gateway()
    {
        return $this->hasOne('Gateway');
    }

    public function details()
    {
        return App::factory('\App\Libraries\AddonHelper')->getDetails($this->directory);
    }

    public static function active()
    {
        return self::where('is_active', '=', '1')->get();
    }

}
