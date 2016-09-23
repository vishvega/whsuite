<?php

class Transaction extends AppModel
{

    public static $types = array(
        'receipt',
        'invoice',
        'debit'
    );

    public function Client()
    {
        return $this->belongsTo('Client');
    }

    public function Invoice()
    {
        return $this->belongsTo('Invoice');
    }

    public function Currency()
    {
        return $this->belongsTo('Currency');
    }

    public function Gateway()
    {
        return $this->belongsTo('Gateway');
    }

    public static function typesList($no_invoice = true)
    {
        $list = array();
        foreach(self::$types as $type)
        {
            $list[$type] = \App::get('translation')->get($type);
        }

        if ($no_invoice) {
            unset($list['invoice']);
        }

        return $list;
    }


}
