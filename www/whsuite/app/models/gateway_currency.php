<?php

class GatewayCurrency extends AppModel
{

    public function Gateway()
    {
        return $this->belongsTo('Gateway');
    }

    public function Currency()
    {
        return $this->belongsTo('Currency');
    }

}
