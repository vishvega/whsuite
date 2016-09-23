<?php

class SettingCategory extends AppModel
{

    public function Setting()
    {
        return $this->hasMany('Setting');
    }

}
