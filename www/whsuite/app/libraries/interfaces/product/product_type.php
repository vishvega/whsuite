<?php

namespace App\Libraries\Interfaces\Product;

interface ProductType
{

    /**
     * load custom tabs handled by the addon
     *
     * @param   array   the product type model so the addon can handle multiple product types based on their slug
     * @return  array   returns an array containing the tab names and view paths
     */
    public function loadProductTabs($product_type);

    /**
     * load custom view data
     *
     * @param   array   the product type model so the addon can handle multiple product types based on their slug
     * @param   array  array of data used in the product just incase the addon needs it
     * @return  array  array of view data to inject
     */
    public function loadViewData($product_type, $data = array());

    /**
     * load custom form data to populate fields
     *
     * @param   array   the product type model so the addon can handle multiple product types based on their slug
     * @param   array  array of data used in the product just incase the addon needs it
     * @return  array  array of view data to inject
     */
    public function loadFormData($product_type, $data = array());

    /**
     * allow validation on any of the fields added by this addon
     *
     * @param   array   the product type model so the addon can handle multiple product types based on their slug
     * @param   array   complete array of all post data to allow the addon to handle it as it sees fit
     * @return   array   array of validation errors returned by the addon (or an empty array if there were none)
     */
    public function validateAddonProductData($product_type, $data);

    /**
     * save custom tab data
     *
     * @param   array   the product type model so the addon can handle multiple product types based on their slug
     * @param   array   the saved product model data to allow the addon to alter and reference back to it
     * @param   array   complete array of all post data to allow the addon to handle it as it sees fit
     * @return   boolean   returns true / false to let whsuite know if the addon successfully added its custom data
     */
    public function saveAddonProductData($product_type, $product, $data);
}
