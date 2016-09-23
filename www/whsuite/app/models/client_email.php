<?php

class ClientEmail extends AppModel
{

    public function Client()
    {
        return $this->belongsTo('Client');
    }

}
