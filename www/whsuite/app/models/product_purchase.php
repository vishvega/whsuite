<?php

class ProductPurchase extends AppModel
{
    /*
     * move towards using constants for statuses
     */
    const
        PENDING    = 0,
        ACTIVE     = 1,
        SUSPENDED  = 2,
        TERMINATED = 3;

    public static $status_types = array(
        '0' => 'pending',
        '1' => 'active',
        '2' => 'suspended',
        '3' => 'terminated'
    );

    public static $rules = array(
        'currency_id' => 'required|integer',
        'billing_period_id' => 'required|integer',
        'first_payment' => 'required|numeric|min:0',
        'recurring_payment' => 'numeric|min:0',
        'next_renewal' => 'date_format:Y-m-d',
        'next_invoice' => 'date_format:Y-m-d',
        'promotion_id' => 'integer|min:0',
        'status' => 'required|integer|min:0|max:3',
        'disable_autosuspend' => 'integer|max:1',
        'gateway_id' => 'integer|min:0'
    );

    public function Product()
    {
        return $this->belongsTo('Product');
    }

    public function ProductPurchaseData()
    {
        return $this->hasMany('ProductPurchaseData');
    }

    public function Client()
    {
        return $this->belongsTo('Client');
    }

    public function Order()
    {
        return $this->belongsTo('Order');
    }

    public function BillingPeriod()
    {
        return $this->belongsTo('BillingPeriod');
    }

    public function Promotion()
    {
        return $this->belongsTo('Promotion');
    }

    public function Gateway()
    {
        return $this->belongsTo('Gateway');
    }

    public function Hosting()
    {
        return $this->hasOne('Hosting');
    }

    public function Domain()
    {
        return $this->hasOne('Domain');
    }

    public function Currency()
    {
        return $this->belongsTo('Currency');
    }

    public function ProductAddonPurchase()
    {
        return $this->hasMany('ProductAddonPurchase');
    }

    public static function formattedStatuses()
    {
        $types = array();
        foreach (self::$status_types as $id => $type) {
            $types[$id] = \App::get('translation')->get($type);
        }
        return $types;
    }

    /**
     * given the id of the product purchased row.
     * return the actual product (either hosting name or domain name: domain registered)
     *
     * @param object|int    model object that contains product_purchase_id or product_purchase_id itself
     * @return string the   product name that was purchased
     */
    public static function getProductName($input)
    {
        if (is_object($input)) {
            $purchase = $input->ProductPurchase()->with('Product.ProductType')->first();
        } else {
            $purchase = self::where('id', '=', $input)->with('Product.ProductType')->first();
        }

        $product = $purchase->Product;
        $product_type = $product->ProductType;

        $return = $product->name;

        if ($product_type->is_domain) {
            $domain = $purchase->Domain()->first();

            $return .= ' (' . $domain->domain . ')';

        } elseif ($product_type->is_hosting) {
            $hosting = $purchase->Hosting()->first();

            $return .= ' (' . $hosting->domain . ')';
        }

        return $return;
    }
}
