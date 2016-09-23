<?php

class ServerModule extends AppModel
{
    public function Addon()
    {
        return $this->belongsTo('Addon');
    }

    public function ServerGroup()
    {
        return $this->hasMany('ServerGroup');
    }
}
