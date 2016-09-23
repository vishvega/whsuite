<?php

class ProductType extends AppModel
{

    public function Product()
    {
        return $this->hasMany('Product');
    }

}
