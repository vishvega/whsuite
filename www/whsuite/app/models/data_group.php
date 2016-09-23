<?php

class DataGroup extends AppModel
{
    public static $rules = array(
        'slug' => 'required|max:100',
        'name' => 'required|max:100',
        'addon_id' => 'integer',
        'is_editable' => 'integer|min:0|max:1',
        'is_active' => 'integer|min:0|max:1'
    );

    public function DataField()
    {
        return $this->hasMany('DataField');
    }

}
