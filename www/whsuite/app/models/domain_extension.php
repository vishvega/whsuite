<?php

class DomainExtension extends AppModel
{
    public static $rules = array(
        'extension' => 'required|max:65',
        'registrar_id' => 'integer|required|min:1',
        'automatic_registration' => 'integer|min:0|max:1',
        'has_eppcode' => 'integer|min:0|max:1',
        'min_years' => 'integer|min:1',
        'max_years' => 'integer|min:1',
        'sort' => 'integer'
    );

    public function Product()
    {
        return $this->belongsTo('Product');
    }

    public function Registrar()
    {
        return $this->belongsTo('Registrar');
    }

    public function DomainPricing()
    {
        return $this->hasMany('DomainPricing');
    }

    public function ContactExtension()
    {
        return $this->belongsTo('ContactExtension');
    }

    /**
     * redefine the save to check that the domain extension was prefixed with a
     * period. If not, we'll add it.
     */
    public function save(array $options = array())
    {
        if (substr($this->extension, 0, 1) != '.') {
            $this->extension = '.' . $this->extension;
        }

        return parent::save($options);
    }

}
