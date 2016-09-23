<?php

class TaxLevel extends AppModel
{
    /**
     * get the level 1 and level 2 taxes for the given state / country
     *
     * @param   string  $state      State to search on
     * @param   string  $country    Country to search on
     * @return  array   $taxLevels  two element array of tax rates found
     */
    public static function getRates($state, $country)
    {
        $taxRates = array(
            'level1' => 0,
            'level2' => 0
        );

        $taxLevel1 = self::where('level', '=', '1')
            ->where('country', '=', $country)
            ->where(function ($query) use ($state) {

                $query->where('state', '=', $state)
                ->orWhere('state', '=', '');

            })->first();

        if (! empty($taxLevel1)) {
            $taxRates['level1'] = $taxLevel1->rate;
        }

        $taxLevel2 = self::where('level', '=', '2')
            ->where('country', '=', $country)
            ->where(function ($query) use ($state) {

                $query->where('state', '=', $state)
                ->orWhere('state', '=', '');

            })->first();

        if (! empty($taxLevel2)) {
            $taxRates['level2'] = $taxLevel2->rate;
        }

        return $taxRates;
    }
}
