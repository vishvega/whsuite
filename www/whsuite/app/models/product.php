<?php

class Product extends AppModel
{
    public static $rules = array(
        'product_type_id' => 'required|integer|min:1',
        'name' => 'required|max:100',
        'description' => '',
        'is_active' => 'integer|min:0|max:1',
        'is_visible' => 'integer|min:0|max:1',
        'domain_type' => 'integer|min:0|max:2',
        'email_template_id' => 'required|integer',
        'stock' => 'integer',
        'server_group_id' => 'integer',
        'auto_suspend_days' => 'integer|min:-1',
        'suspend_email_template_id' => 'required|integer',
        'auto_terminate_days' => 'integer|min:-1',
        'terminate_email_template_id' => 'required|integer',
        'charge_disk_overages' => 'integer|min:0|max:1',
        'charge_bandwidth_overages' => 'integer|min:0|max:1',
        'allow_ips' => 'integer|min:0|max:1',
        'included_ips' => 'integer|min:0',
        'allow_upgrades' => 'integer|min:0|max:1',
        'is_taxed' => 'integer|min:0|max:1',
        'affiliate_is_enabled' => 'integer|min:0|max:1',
        'affiliate_is_recurrin' => 'integer|min:0|max:1',
        'affiliate_amount' => 'numeric|min:0',
        'sort' => 'integer|min:0'
    );

    public static $product_setup_options = array(
        '0' => 'manually',
        '1' => 'on_payment_confirmation',
        '2' => 'on_order_creation'
    );

    public static $product_domain_options = array(
        '0' => 'no_domain_needed',
        '1' => 'enter_own_domain_name',
        '2' => 'enter_hostname'
    );

    public function ProductType()
    {
        return $this->belongsTo('ProductType');
    }

    public function ProductGroup()
    {
        return $this->belongsTo('ProductGroup');
    }

    public function ProductPurchase()
    {
        return $this->hasMany('ProductPurchase');
    }

    public function ServerGroup()
    {
        return $this->belongsTo('ServerGroup');
    }

    public function ProductData()
    {
        return $this->hasMany('ProductData');
    }

    public function DomainExtension()
    {
        return $this->hasOne('DomainExtension');
    }

    public function ProductPricing()
    {
        return $this->hasMany('ProductPricing');
    }

    public function ProductAddonProduct()
    {
        return $this->hasMany('ProductAddonProduct');
    }
}
