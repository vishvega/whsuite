<?php

use \Illuminate\Support\Str;
use \Whsuite\Inputs\Post as PostInput;

class ProductAddonsController extends AdminController
{
    /**
     * scaffolding overrides for addon listing
     *
     * see admin base controller for doc blocks
     */
    protected function indexToolbar()
    {
        return array(
            array(
                'url_route' => 'admin-productaddon',
                'link_class' => '',
                'icon' => 'fa fa-list-ul',
                'label' => 'productaddon_management'
            ),
            array(
                'url_route' => 'admin-productaddon-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'productaddon_add'
            )
        );
    }

    protected function indexColumns()
    {
        return array(
            array(
                'field' => 'name'
            ),
            array(
                'field' => 'updated_at'
            ),
            array(
                'action' => 'edit',
                'label' => null
            ),
            array(
                'action' => 'delete',
                'label' => null
            )
        );
    }

    protected function indexActions()
    {
        return array(
            'edit' => array(
                'url_route' => 'admin-productaddon-edit',
                'link_class' => 'btn btn-primary btn-small pull-right',
                'icon' => 'fa fa-pencil',
                'label' => 'edit',
                'params' => array('id')
            ),
            'delete' => array(
                'url_route' => 'admin-productaddon-delete',
                'link_class' => 'btn btn-danger btn-small pull-right',
                'icon' => 'fa fa-remove',
                'label' => 'delete',
                'params' => array('id')
            )
        );
    }

    protected function formFields()
    {
        $fields = array(
            'ProductAddon.id',
            'ProductAddon.name',
            'ProductAddon.addon_slug',
            'ProductAddon.addon_value',
            'ProductAddon.description' => array(
                'type' => 'textarea'
            ),
            'ProductAddon.is_free' => array(
                'type' => 'checkbox'
            )
        );

        return $fields;
    }

    protected function afterSave(&$main_model)
    {
        // Save product data
        $product_data = PostInput::get('data.Products');

        foreach ($product_data as $product_id => $value) {
            // check if there is already a record for this product.
            $product_addon_product = ProductAddonProduct::where('product_addon_id', '=', $main_model->id)->where('product_id', '=', $product_id)->first();

            if (! empty($product_addon_product)) {
                if ($value == '0') {
                    // A record was found and we need to delete it
                    $product_addon_product->delete();
                }
            } elseif ($value == '1') {
                $product_addon_product = new ProductAddonProduct();
                $product_addon_product->product_addon_id = $main_model->id;
                $product_addon_product->product_id = $product_id;
                $product_addon_product->save();
            }
        }

        // Save pricing data
        $pricing_data = PostInput::get('data.ProductAddonPricing');
        foreach ($pricing_data as $billing_period_id => $price_data) {
            foreach ($price_data as $currency_id => $price) {
                // Check if a record already exists
                $pricing = ProductAddonPricing::where('addon_id', '=', $main_model->id)->where('currency_id', '=', $currency_id)->where('billing_period_id', '=', $billing_period_id)->first();

                if ($pricing && $price == '') {
                    // The pricing record exists, but we want to remove it now as
                    // it's no longer needed.
                    $pricing->delete();
                } elseif ($price !='') {
                    if (! $pricing) {
                        $pricing = new ProductAddonPricing();
                    }

                    $pricing->addon_id = $main_model->id;
                    $pricing->currency_id = $currency_id;
                    $pricing->billing_period_id = $billing_period_id;
                    $pricing->price = $price;

                    $pricing->save();
                }
            }
        }
    }

    protected function getExtraData($model)
    {
        // Product data
        $products = Product::all();
        $this->view->set('products', $products);

        $product_values = array();
        if (!is_null($model->id)) {
            $title = $this->lang->get('edit_product_addon');

            foreach ($products as $product) {
                $product_addon_products = ProductAddonProduct::where('product_id', '=', $product->id)->where('product_addon_id', '=', $model->id)->first();

                if (! empty($product_addon_products)) {
                    $product_values[$product->id] = '1';
                }
            }

            PostInput::set('data.Products', $product_values);
        }

        // Pricing
        $currencies = Currency::all();
        $this->view->set('currencies', $currencies);

        $billing_periods = BillingPeriod::all();
        $this->view->set('billing_periods', $billing_periods);

        $pricing = array();

        if (! is_null($model->id)) {
            $addon_pricing = ProductAddonPricing::where('addon_id', '=', $model->id)->get();

            if (! empty($addon_pricing)) {
                foreach ($addon_pricing as $price) {
                    $pricing[$price->billing_period_id][$price->currency_id] = $price->price;
                }
            }

            PostInput::set('data.ProductAddonPricing', $pricing);
        }
    }

    /**
     * form function override for template
     */
    public function form($id = null)
    {
        // override
        $this->render_view = 'product_addons/form.php';
        return parent::form($id);
    }

    /**
     * delete function override to prevent deleting of an addon that's being used
     * by a product purchase.
     */
    public function delete($id)
    {
        $purchases = ProductAddonPurchase::where('addon_id', '=', $id);

        if ($purchases->count() < 1) {
            // Delete pricing
            ProductAddonPricing::where('addon_id', '=', $id)->delete();

            // Delete product linkage
            ProductAddonProduct::where('product_addon_id', '=', $id)->delete();

            return parent::delete($id);
        } else {
            App::get('session')->setFlash('error', $this->lang->get('cant_delete_assigned_product_addons'));
            return $this->redirect('admin-productaddon');
        }
    }
}
