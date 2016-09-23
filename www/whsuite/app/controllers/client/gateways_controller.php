<?php

/**
 * Client Gateways Controller
 *
 * Simply used to check during ajax if a gateway has a unique payment button
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class GatewaysController extends GatewayBaseController
{
    /**
     * check if a gateway as a payment button in the specific route
     * 'ajax-GATEWAY-pay-button'
     *
     * @param   string      the gateway to check
     * @return  HTTP Response
     */
    public function hasPayButton($gateway, $invoice_id)
    {
        try {
            $generate = \App::get('router')->generate(
                'ajax-' . $gateway . '-pay-button',
                array(
                    'invoice_id' => $invoice_id
                )
            );

            $return = array(
                'result' => true,
                'gateway_url' => $generate,
                'default_btn' => $this->view->fetch('elements/pay-invoice-btn.php')
            );

        } catch (Exception $e) {

            $return = array(
                'result' => false
            );
        }

        $Http = new \Whsuite\Http\Http;
        $Response = $Http->newResponse('json');

        $Response->setContent($return);

        // send the response
        $Http->send($Response);
    }

    /**
     * load the default invoice payment button
     *
     * @return  HTTP Response
     */
    public function defaultPayButton()
    {
        $return = array(
            'result' => true,
            'html' => $this->view->fetch('elements/pay-invoice-btn.php')
        );

        $Http = new \Whsuite\Http\Http;
        $Response = $Http->newResponse('json');
        $response->setContent($return);

        // send the response
        $Http->send($Response);
    }

}
