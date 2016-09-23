<?php

class ProductData extends AppModel
{
    public function Product()
    {
        return $this->belongsTo('Product');
    }
}
