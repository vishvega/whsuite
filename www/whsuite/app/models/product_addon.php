<?php

class ProductAddon extends AppModel
{
    public static $rules = array(
        'name' => 'required',
        'addon_slug' => 'required'
    );

    public static function addonList($product_id, $null_row = false)
    {
        $addon_list = array();

        $product_addon_products = ProductAddonProduct::where('product_id', '=', $product_id)->get();
        $addon_ids = array();
        foreach($product_addon_products as $addon_product) {
            $addon_ids[] = $addon_product->product_addon_id;
        }
        if(empty($addon_ids)) {
            return $addon_list;
        }

        $addons = self::whereIn('id', $addon_ids)->get();

        if ($null_row) {
            $addon_list[0] = App::get('translation')->get('not_available');
        }

        if (count($addons) > 0) {
            foreach ($addons as $addon) {
                $addon_list[$addon->id] = $addon->name;
            }
        }

        return $addon_list;
    }

}
