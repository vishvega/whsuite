<?php
/**
 * Client Profile Controller
 *
 * The profile controller handles the editing of the client profile
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class ProfileController extends ClientController
{
    /**
     * Index
     *
     * This is the dashboard of the admin area.
     */
    public function index($page = 1, $per_page = null)
    {
        if (!$this->logged_in) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $client = Client::find($this->client->id);

        if (\Whsuite\Inputs\Post::Get()) {

            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Client'), Client::$rules);
            $cf_validator = $client->validateCustomFields();

            if (! $validator->fails() && ($cf_validator['result'] == '1')) {

                // Passed validation. Now lets update the client data
                $client_data = \Whsuite\Inputs\Post::get('Client'); // Set the client data into a var to make things easier.

                $client->first_name = $client_data['first_name'];
                $client->last_name = $client_data['last_name'];
                $client->company = $client_data['company'];
                $client->email = $client_data['email'];
                $client->html_emails = $client_data['html_emails'];
                $client->address1 = $client_data['address1'];
                $client->address2 = $client_data['address2'];
                $client->city = $client_data['city'];
                $client->state = $client_data['state'];
                $client->postcode = $client_data['postcode'];
                $client->country = $client_data['country'];
                $client->phone = $client_data['phone'];
                $client->language_id = $client_data['language_id'];

                if ($client_data['password'] !='' && $client_data['confirm_password'] !='') {
                    $client->password = $client_data['password'];
                }

                try {
                    if ($client->save() && $client->saveCustomFields()) {

                        App::get('session')->setFlash('success', $this->lang->get('profile_updated'));
                    } else {

                        App::get('session')->setFlash('error', $this->lang->get('error_updating_profile'));
                    }

                    // catch the sentry exceptions for validation
                } catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {

                    // no email
                    App::get('session')->setFlash('error',
                        $this->lang->formatErrors(
                            json_encode(
                                array(
                                    'email' => array(
                                        'validation.required'
                                    )
                                )
                            )
                        )
                    );

                } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {

                    // email exists
                    App::get('session')->setFlash('error',
                        $this->lang->formatErrors(
                            json_encode(
                                array(
                                    'email' => array(
                                        'validation.unique'
                                    )
                                )
                            )
                        )
                    );
                }

                return header("Location: ".App::get('router')->generate('client-profile'));

            } elseif ($cf_validator['result'] != '1') {
                \App\Libraries\Message::set($this->lang->formatErrors($cf_validator['errors']), 'fail');
            } else {
                // The validation failed. Show the reason why.
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            }
        }

        \Whsuite\Inputs\Post::set('Client', $client->toArray());

        $title = $this->lang->get('edit_details');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('country_list', Country::getCountries());
        $this->view->set('client', $client);

        $this->view->display('profile/index.php');
    }

}
