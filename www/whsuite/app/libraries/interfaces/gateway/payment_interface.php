<?php

namespace App\Libraries\Interfaces\Gateway;

interface PaymentInterface
{
    /**
     * setup a payment, load the omnipay module
     * setup params, account names / amount etc..
     *
     * @param   array   array of data in order to perform the transaction
     * @param   bool    Indicator of whether we're setting up for check return
     * @return  bool    return true / false in order to proceed with the transaction
     */
    public function setup($data, $returnSetup = false);


    /**
     * process the payment
     *
     * @param   array   array of data in order to perform the transaction
     */
    public function process($data);


    /**
     * check the return of a payment
     *
     * @param   array   array of data in order to perform the transaction
     * @return  bool|string
     */
    public function checkReturn($data);

}
