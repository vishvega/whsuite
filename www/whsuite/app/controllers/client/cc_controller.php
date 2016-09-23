<?php
/**
 * Client Credit Card Controller
 *
 * The credit card controller handles both credit and debit cards, providing a
 * CRUD interface for them in the client area.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class CcController extends ClientController
{
    /**
     * Manage Cc
     */
    public function manageCc($id)
    {
        if($this->logged_in) {
            // Check that the card exists, and that it belongs to this client.
            $cc = ClientCc::getCc($id);
            if (!empty($cc) && $cc->client_id === $this->client->id) {

                // The card exists, and belongs to this client. We're good to go!
                $data = \Whsuite\Inputs\Post::get();

                if (isset($data) && ! empty($data)) {

                    $validator = $this->validator->make($data['Cc'], ClientCc::$rules);
                    if (!$validator->fails()) {
                        // Validation passed

                        // Passed validation. Now lets update the data
                        $data = $data['Cc']; // Set the data into a var to make things easier.

                        // Because the model might return its own error, we're going to
                        // do the true/false check slightly differently. If the returned
                        // data is 'null' we instead dont try to show a message as it will
                        // mean it's already been set by the model.
                        $cc_result = ClientCc::saveCc($data, $cc->id, $this->client->id);
                        if ($cc_result) {

                            App::get('session')->setFlash('success', $this->lang->get('credit_card_successfully_saved'));
                        } else {

                            App::get('session')->setFlash('error', $this->lang->get('error_saving_credit_card'));
                        }
                        header("Location: ".App::get('router')->generate('client-billing'));
                    } else {
                        \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'error');
                    }
                }

                \Whsuite\Inputs\Post::set('Cc', $cc->toArray());

                $title = $this->lang->get('edit_cc');
                $this->view->set('title', $title);

                App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
                App::get('breadcrumbs')->add($this->lang->get('manage_billing_details'), 'client-billing');
                App::get('breadcrumbs')->add($title);
                App::get('breadcrumbs')->build();

                $this->view->set('country_list', Country::getCountries());
                $this->view->set('cc', $cc);
                return $this->view->display('cc/manageCc.php');


            }
        }
        return header("Location: ".App::get('router')->generate('client-home'));
    }

    public function addCc()
    {
        if($this->logged_in) {

            $data = \Whsuite\Inputs\Post::get();

            if (isset($data) && ! empty($data)) {

                $validator = $this->validator->make($data['Cc'], ClientCc::$rules);

                if (!$validator->fails()) {
                    // Validation passed

                    // Passed validation. Now lets update the data
                    $data = $data['Cc']; // Set the data into a var to make things easier.

                    // Because the model might return its own error, we're going to
                    // do the true/false check slightly differently. If the returned
                    // data is 'null' we instead dont try to show a message as it will
                    // mean it's already been set by the model.
                    $cc_result = ClientCc::saveCc($data, 0, $this->client->id);
                    if ($cc_result === true) {

                        App::get('session')->setFlash('success', $this->lang->get('credit_card_successfully_saved'));
                    } elseif ($cc_result === false) {

                        App::get('session')->setFlash('error', $this->lang->get('error_saving_credit_card'));
                    }
                } else {
                    App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
                }
            }
        }
        return header("Location: ".App::get('router')->generate('client-billing'));
    }

    public function deleteCc($id)
    {
        if($this->logged_in) {
            $cc = ClientCc::find($id);

            if(!empty($cc) && $cc->client_id === $this->client->id) {
                if($cc->delete()) {
                    App::get('session')->setFlash('success', $this->lang->get('credit_card_successfully_deleted'));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('error_deleting_credit_card'));
                }
            }
        }
        return header("Location: ".App::get('router')->generate('client-billing'));
    }
}
