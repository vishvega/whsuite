<?php
/**
 * Gateway Base Controller
 *
 * Gateway base controller, mainly for gateways that
 * have a button that needs to be populated differently
 * E.G. Stripe Charges
 *
 * @package  WHSuite-Controllers
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */


class GatewayBaseController extends AppController
{
    public function onLoad()
    {
        // Set client theme
        App::get('view')->setTheme(App::get('configs')->get('settings.frontend.client_theme'));

        parent::onLoad();
    }

    /**
     * shortcut function for devs to use to return
     * the data in the correct format
     *
     * @param   string  HTML button to return
     * @return  HTTP Response
     */
    protected function returnButton($button)
    {
        if (is_string($button)) {

            $return = array(
                'result' => true,
                'html' => $button
            );
        } else {

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

}
