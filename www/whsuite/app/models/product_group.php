<?php

class ProductGroup extends AppModel
{
    protected $custom_fields = true;
    protected $custom_fields_slug = 'product_group_fields';

    public static $rules = array(
        'name' => 'required|max:100',
        'is_visible' => 'integer|min:0|max:1',
        'sort' => 'integer|min:0'
    );

    public function Product()
    {
        return $this->hasMany('Product');
    }


}
