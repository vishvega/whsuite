<?php

class ProductAddonProduct extends AppModel
{
    public function Product()
    {
        return $this->belongsTo('Product');
    }

    public function ProductAddon()
    {
        return $this->belongsTo('ProductAddon', 'product_addon_id');
    }
}
