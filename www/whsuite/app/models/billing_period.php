<?php

class BillingPeriod extends AppModel
{
    public static $rules = array(
        'name' => 'required|max:30',
        'days' => 'integer'
    );



}
