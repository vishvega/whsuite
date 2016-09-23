<?php

class MenuLink extends AppModel
{
    public static $rules = array(
        'title' => 'required|max:100',
        'parent_id' => 'integer',
        'is_link' => 'integer|min:0|max:1',
        'url' => 'required|max:255',
        'sort' => 'integer|min:0',
        'clients_only' => 'integer|min:0|max:1',
        'class' => 'max:255'
    );

    public function links()
    {
        return $this->belongsTo('MenuGroup');
    }

    public function children()
    {
        return $this->hasMany('MenuLink', 'parent_id');
    }
}
