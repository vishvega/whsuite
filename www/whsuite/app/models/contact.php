<?php

class Contact extends AppModel
{
    public static $rules = array(
        'contact_type' => 'required',
        'title' => 'required',
        'first_name' => 'required|max:100',
        'last_name' => 'required|max:100',
        'company' => 'max:100',
        'email' => 'email|required|max:150',
        'address1' => 'max:150|required',
        'address2' => 'max:150',
        'address3' => 'max:150',
        'city' => 'max:150|required',
        'state' => 'max:150|required',
        'postcode' => 'max:50|required',
        'country' => 'max:255|required',
        'phone' => 'max:25|required',
    );

    public function ContactExtension()
    {
        return $this->hasOne('ContactExtension');
    }

}
