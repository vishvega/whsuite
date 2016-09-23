<?php

class Country extends AppModel
{
    public function getNameByIsoCode($iso_code)
    {
        $country = $this->getByIsoCode($iso_code);

        return $country->name;
    }

    public function getByIsoCode($iso_code)
    {
        return Country::where('iso_code', '=', $iso_code)->get();
    }

    /**
     * Get countries for a drop down list. Option to add a blank row at the beginning of array
     *
     * @param    bool       Option to add a blank row at the beginning of the array
     * @return   array      Array of countries
     */
    public static function getCountries($null_row = false)
    {

        $countries = parent::formattedList('name', 'name', array(), 'name', 'asc');

        if($null_row) {
            $countries = array_merge(array('0' => ''), $countries);
        }

        return $countries;

    }
}
