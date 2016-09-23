<?php

class ServerIp extends AppModel
{
    public function Server()
    {
        return $this->belongsTo('Server');
    }

    public function ProductPurchase()
    {
        return $this->belongsTo('ProductPurchase');
    }

    public static function ipList($server_id = '0', $product_purchase_id = '0', $null_row = false)
    {
        $ips = self::where('product_purchase_id', '=', $product_purchase_id)->where('server_id', '=', $server_id)->get();

        $ip_list = array();
        if($null_row) {

            $ip_list[0] = \App::get('translation')->get('not_available');
        }

        foreach($ips as $ip) {

            $ip_list[$ip->id] = $ip->ip_address;
        }

        return $ip_list;
    }
}
