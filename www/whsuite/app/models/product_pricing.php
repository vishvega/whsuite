<?php

class ProductPricing extends AppModel
{
    public static $rules = array(
        'product_id' => 'required|integer',
        'currency_id' => 'required|integer',
        'billing_period_id' => 'required|integer',
        'price' => 'required|numeric',
        'bandwidth_overage_fee' => 'numeric',
        'diskspace_overage_fee' => 'numeric',
        'setup' => 'numeric',
        'allow_in_signup' => 'integer|min:0|max:1'
    );

    public function Product()
    {
        return $this->belongsTo('Product');
    }

    public function BillingPeriod()
    {
        return $this->belongsTo('BillingPeriod');
    }

    public function Currency()
    {
        return $this->belongsTo('Currency');
    }
}
