<?php

class MenuGroup extends AppModel
{
    public static $rules = array(
        'name' => 'required|max:100',
    );

    public function links()
    {
        return $this->hasMany('MenuLink');
    }

}
