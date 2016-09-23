<?php

class Gateway extends AppModel
{

    public function Addon()
    {
        return $this->belongsTo('Addon');
    }

    /**
     * function to easily get gateways in formatted list without passing all params
     *
     * @param   bool    Only bring back the active gateways
     * @param   bool    Whether or not to add a blank row to the array
     * @return  array   Array of gateways
     */
    public static function getGateways($active = true, $null_row = false)
    {
        if ($active) {

            return parent::formattedList(
                'id' ,
                'name',
                array(
                    array(
                        'type' => 'and',
                        'column' => 'is_active',
                        'operator' => '=',
                        'value' => '1'
                    )
                ),
                'sort',
                'desc',
                $null_row
            );

        } else {

            return parent::formattedList(
                'id' ,
                'name',
                array(),
                'sort',
                'desc',
                $null_row
            );
        }
    }

    public function GatewayCurrency()
    {
        return $this->hasMany('GatewayCurrency');
    }
}
