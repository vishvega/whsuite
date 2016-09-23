<?php

class Domain extends AppModel
{

    public static $rules = array(
        'registrar_id' => 'required|integer|min:0',
        'renewal_disabled' => 'integer|min:0|max:1'
    );

    public function Registrar()
    {
        return $this->belongsTo('Registrar');
    }

    public function ProductPurchase()
    {
        return $this->belongsTo('ProductPurchase');
    }

}
