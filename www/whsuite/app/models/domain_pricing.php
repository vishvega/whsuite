<?php

class DomainPricing extends AppModel
{
    public function DomainExtension()
    {
        return $this->belongsTo('DomainExtension');
    }

    public function Currency()
    {
        return $this->belongsTo('Currency');
    }
}
