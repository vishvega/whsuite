<?php

class ServerGroup extends AppModel
{

    protected $custom_fields = true;
    protected $custom_fields_slug = 'server_group_fields';

    public static $rules = array(
        'name' => 'required|max:100',
        'autofill' => 'integer|min:0|max:1',
        'server_module_id' => 'integer|min:0'
    );

    public function ServerModule()
    {
        return $this->belongsTo('ServerModule');
    }

    public function Server()
    {
        return $this->hasMany('Server');
    }

}
