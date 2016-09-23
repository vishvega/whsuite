<?php

class Hosting extends AppModel
{

    public static $rules = array(
        'server_id' => 'required|integer',
        'domain' => '',
        'nameservers' => '',
        'diskspace_limit' => 'numeric|min:0',
        'bandwidth_limit' => 'numeric|min:0',
        'status' => 'int|min:0',
        'username' => ''
    );

    public function Server()
    {
        return $this->belongsTo('Server');
    }

    public function ProductPurchase()
    {
        return $this->belongsTo('ProductPurchase');
    }

}
