<?php

class Registrar extends AppModel
{

    public function Domain()
    {
        return $this->hasMany('Domain');
    }

    public function DomainExtension()
    {
        return $this->hasMany('DomainExtension');
    }

    public function Addon()
    {
        return $this->belongsTo('Addon');
    }

}
