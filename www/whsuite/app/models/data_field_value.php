<?php

class DataFieldValue extends AppModel
{

    public function DataField()
    {
        return $this->belongsTo('DataField');
    }

}
