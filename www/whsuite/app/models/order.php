<?php

class Order extends AppModel
{

    public function Client()
    {
        return $this->belongsTo('Client');
    }

    public function Gateway()
    {
        return $this->hasOne('Gateway');
    }

    public function InvoiceItem()
    {
        return $this->hasMany('InvoiceItem');
    }

    public function ProductPurchase()
    {
        return $this->hasMany('ProductPurchase');
    }

    public function Invoice()
    {
        return $this->belongsTo('Invoice');
    }

    /**
     * count the new orders for the shortcut label
     *
     * @return  int       number of new orders
     */
    public static function countNew()
    {
        $instance = new static;
        $query = $instance->newQuery();

        return $query->where('status', '=', 0)
            ->count();
    }

    /**
     * over ride the paginate method to change the default ordering
     *
     */
    public static function paginate(
        $per_page,
        $page,
        $conditions = array(),
        $sort_by = 'id',
        $sort_order = 'desc',
        $route = null,
        $params = array()
    ) {
        return parent::paginate(
            $per_page,
            $page,
            $conditions,
            $sort_by,
            $sort_order,
            $route,
            $params
        );
    }

}
