<?php

class DataField extends AppModel
{

    public static $rules = array(
        'slug' => 'required|max:100',
        'title' => 'required|max:100',
        'type' => 'required|max:30',
        'help_text' => 'max:255',
        'placeholder' => 'max:255',
        'is_editable' => 'integer|min:0|max:1',
        'is_staff_only' => 'integer|min:0|max:1',
        'sort' => 'integer|min:0'
    );

    public static $field_types = array(
        'text',
        'select',
        'textarea',
        'checkbox',
        'wysiwyg'
    );

    public function DataFieldValue()
    {
        return $this->hasMany('DataFieldValue');
    }
}
