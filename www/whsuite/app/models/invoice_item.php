<?php

class InvoiceItem extends AppModel
{

    public function Invoice()
    {
        return $this->belongsTo('Invoice');
    }

    public function ProductPurchase()
    {
        return $this->belongsTo('ProductPurchase');
    }

    public function ProductAddonPurchase()
    {
        return $this->belongsTo('ProductAddonPurchase');
    }

    /**
     * redefine the save to fix totals with commas in before saving
     */
    public function save(array $options = array())
    {
        $this->total = str_replace(',', '', $this->total);

        return parent::save($options);
    }

}
