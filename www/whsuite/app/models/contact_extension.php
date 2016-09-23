<?php

class ContactExtension extends AppModel
{
    public static $rules = array(
        'name' => 'required|max:45',
        'extension_id' => 'integer|min:0',
        'is_active' => 'integer|min:0|max:1'
    );





}
