<?php

class ProductAddonPurchase extends AppModel
{

    public function ProductAddon()
    {
        return $this->belongsTo('ProductAddon', 'addon_id');
    }

}
