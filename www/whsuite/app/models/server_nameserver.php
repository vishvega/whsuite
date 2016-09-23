<?php

class ServerNameserver extends AppModel
{

    public static $rules = array(
        'hostname' => array(
            'required',
            'hostname',
            'max:255'
        ),
        'ip_address' => 'required|ip'
    );

    public function Server()
    {
        return $this->belongsTo('Server');
    }

}
