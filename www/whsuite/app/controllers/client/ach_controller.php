<?php
/**
 * Automated Clearing House Controller
 *
 * The automated clearing house controller handles both ach accounts, providing a
 * CRUD interface for them in the client area.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class AchController extends ClientController
{
    /**
     * Manage Ach
     */
    public function manageAch($id)
    {
        if($this->logged_in) {
            // Check that the ach account exists, and that it belongs to this client.
            $ach = ClientAch::getAch($id);
            if (!empty($ach) && $ach->client_id === $this->client->id) {

                // The ach account exists, and belongs to this client. We're good to go!
                if (\Whsuite\Inputs\Post::get()) {

                    $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Ach'), ClientAch::$rules);
                    if (!$validator->fails()) {
                        // Validation passed

                        // Passed validation. Now lets update the data
                        $data = \Whsuite\Inputs\Post::get('Ach'); // Set the data into a var to make things easier.

                        // Because the model might return its own error, we're going to
                        // do the true/false check slightly differently. If the returned
                        // data is 'null' we instead dont try to show a message as it will
                        // mean it's already been set by the model.
                        $ach_result = ClientAch::saveAch($data, $ach->id, $this->client->id);
                        if ($ach_result === true) {

                            App::get('session')->setFlash('success', $this->lang->get('ach_account_successfully_saved'));
                        } elseif ($ach_result === false) {

                            App::get('session')->setFlash('error', $this->lang->get('error_saving_ach_account'));
                        }
                        header("Location: ".App::get('router')->generate('client-billing'));
                    } else {
                        \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()));
                    }
                }

                \Whsuite\Inputs\Post::set('Ach', $ach->toArray());

                $title = $this->lang->get('edit_ach');
                $this->view->set('title', $title);

                App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
                App::get('breadcrumbs')->add($this->lang->get('manage_billing_details'), 'client-billing');
                App::get('breadcrumbs')->add($title);
                App::get('breadcrumbs')->build();

                $this->view->set('country_list', Country::getCountries());
                $this->view->set('ach', $ach);
                $this->view->set('ach_account_types', ClientAch::accountTypes());
                return $this->view->display('ach/manageAch.php');
            }
        }
        return header("Location: ".App::get('router')->generate('client-home'));
    }

    public function addAch()
    {
        if($this->logged_in) {
            if (\Whsuite\Inputs\Post::get()) {
                $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Ach'), ClientAch::$rules);
                if (!$validator->fails()) {
                    // Validation passed

                    // Passed validation. Now lets update the data
                    $data = \Whsuite\Inputs\Post::get('Ach'); // Set the data into a var to make things easier.

                    // Because the model might return its own error, we're going to
                    // do the true/false check slightly differently. If the returned
                    // data is 'null' we instead dont try to show a message as it will
                    // mean it's already been set by the model.
                    $ach_result = ClientAch::saveAch($data, 0, $this->client->id);
                    if ($ach_result === true) {

                        App::get('session')->setFlash('success', $this->lang->get('ach_account_successfully_saved'));
                    } elseif ($ach_result === false) {

                        App::get('session')->setFlash('error', $this->lang->get('error_saving_ach_account'));
                    }
                } else {
                    App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
                }
            }
        }
        return header("Location: ".App::get('router')->generate('client-billing'));
    }

    public function deleteAch($id)
    {
        if ($this->logged_in) {
            $ach = ClientAch::find($id);

            if (!empty($ach) && $ach->client_id === $this->client->id) {
                if($ach->delete()) {
                    App::get('session')->setFlash('success', $this->lang->get('ach_account_successfully_deleted'));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('error_deleting_ach_account'));
                }
            }
        }
        return header("Location: ".App::get('router')->generate('client-billing'));
    }
}
