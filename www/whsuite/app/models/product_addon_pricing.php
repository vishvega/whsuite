<?php

class ProductAddonPricing extends AppModel
{
    public function BillingPeriod()
    {
        return $this->belongsTo('BillingPeriod');
    }
}
