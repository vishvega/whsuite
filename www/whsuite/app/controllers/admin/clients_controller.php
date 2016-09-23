<?php
/**
 * Clients Admin Controller
 *
 * The clients admin controller handles all client related admin methods.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class ClientsController extends AdminController
{
    public function index($page = 1, $per_page = null)
    {
        $title = $this->lang->get('client_management');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $conditions = array(
            array(
                'column' => 'guest_account',
                'operator' => '=',
                'value' => 0
            )
        );

        $get = \Whsuite\Inputs\Get::get();

        if ($get) {
            if (isset($get['status']) && $get['status'] !='') {
                $conditions[] = array(
                    'column' => 'status',
                    'operator' => '=',
                    'value' => $get['status']
                );
            }
            if (isset($get['first_name']) && $get['first_name'] !='') {
                $conditions[] = array(
                    'column' => 'first_name',
                    'operator' => 'LIKE',
                    'value' => $get['first_name']
                );
            }
            if (isset($get['last_name']) && $get['last_name'] !='') {
                $conditions[] = array(
                    'column' => 'last_name',
                    'operator' => 'LIKE',
                    'value' => $get['last_name']
                );
            }
            if (isset($get['email_address']) && $get['email_address'] !='') {
                $conditions[] = array(
                    'column' => 'email',
                    'operator' => 'LIKE',
                    'value' => $get['email_address']
                );
            }
            if (isset($get['company_name']) && $get['company_name'] !='') {
                $conditions[] = array(
                    'column' => 'company',
                    'operator' => 'LIKE',
                    'value' => $get['company_name']
                );
            }
            if (isset($get['address1']) && $get['address1'] !='') {
                $conditions[] = array(
                    'column' => 'address1',
                    'operator' => 'LIKE',
                    'value' => $get['address1']
                );
            }
            if (isset($get['address2']) && $get['address2'] !='') {
                $conditions[] = array(
                    'column' => 'address2',
                    'operator' => 'LIKE',
                    'value' => $get['address2']
                );
            }
            if (isset($get['post_code']) && $get['post_code'] !='') {
                $conditions[] = array(
                    'column' => 'postcode',
                    'operator' => 'LIKE',
                    'value' => $get['post_code']
                );
            }
            if (isset($get['country']) && $get['country'] !='' && $get['country'] !='0') {
                $conditions[] = array(
                    'column' => 'country',
                    'operator' => '=',
                    'value' => $get['country']
                );
            }
        }

        $clients = Client::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions);

        $active_clients = Client::Member()->where('status', '=', '1')
            ->where('activated', '=', '1')
            ->count();

        $inactive_clients = Client::Member()->where('status', '!=', '1')
            ->orWhere('activated', '!=', '1')
            ->count();

        $fraud_clients = Client::Member()->where('status', '=', '2')
            ->count();

        $this->view->set(
            array(
                'clients' => $clients,
                'country_list' => Country::getCountries(true),
                'active_clients' => $active_clients,
                'inactive_clients' => $inactive_clients,
                'fraud_clients' => $fraud_clients,
                'total_clients' => Client::Member()->count()
            )
        );

        // Sadly because of an annoying limitation in PHP we cant do this next bit directly in the client model.
        $status_types = array('' => '');
        foreach (Client::$status_types as $id => $status) {
            $status_types[$id] = App::get('translation')->get($status);
        }

        $this->view->set('status_types', $status_types);

        $toolbar = array(
            array(
                'url_route'=> 'admin-client-add',
                'icon' => 'fa fa-plus',
                'label' => 'add_client'
            ),
        );
        $this->view->set('toolbar', $toolbar);

        $this->view->display('clients/list.php');
    }

    public function addClient()
    {
        if (\Whsuite\Inputs\Post::get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Client'), Client::$rules);
            $client = new Client();

            if ($validator->fails()) {
                \App\Libraries\Message::set(
                    $this->lang->formatErrors(
                        json_encode($validator->messages()->toArray())
                    ),
                    'fail'
                );
            } elseif (! $client->validateCustomFields(false)) {
                // For now we set this as a flash error. In a future update we'll prevent the need to redirect/reload.
                App::get('session')->setFlash('error', $this->lang->get('error_adding_client'));
                return $this->redirect('admin-client-add');
            } else {
                // Passed validation. Now lets update the client data
                $client_data = \Whsuite\Inputs\Post::get('Client'); // Set the client data into a var to make things easier.

                // just check it's not a guest account
                $guest_client = Client::where('email', '=', $client_data['email'])
                    ->where('guest_account', '=', 1)
                    ->first();

                // we have a guest client account
                if (! empty($guest_client)) {
                    $client = $guest_client;
                }

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
                $client->status = $client_data['status'];
                $client->language_id = $client_data['language_id'];
                $client->currency_id = $client_data['currency_id'];
                $client->is_taxexempt = $client_data['is_taxexempt'];
                $client->guest_account = 0;

                if ($client->status == '1') {
                    $client->activated = '1';
                }

                if (isset($client_data['password']) && $client_data['password'] !='') {
                    $client->password = $client_data['password'];
                }

                try {
                    if ($client->save() && $client->saveCustomFields(false)) {
                        App::get('session')->setFlash('success', $this->lang->get('client_added'));
                        return $this->redirect('admin-client-profile', ['id' => $client->id]);

                    } else {
                        \App\Libraries\Message::set($this->lang->get('scaffolding_save_error'), 'fail');
                    }

                    // catch the sentry exceptions for validation
                } catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
                    // no email
                    \App\Libraries\Message::set(
                        $this->lang->formatErrors(
                            json_encode(
                                array(
                                    'email' => array(
                                        'validation.required'
                                    )
                                )
                            )
                        ),
                        'fail'
                    );

                } catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e) {
                    // no password
                    \App\Libraries\Message::set(
                        $this->lang->formatErrors(
                            json_encode(
                                array(
                                    'password' => array(
                                        'validation.required'
                                    )
                                )
                            )
                        ),
                        'fail'
                    );

                } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
                    // email exists
                    \App\Libraries\Message::set(
                        $this->lang->formatErrors(
                            json_encode(
                                array(
                                    'email' => array(
                                        'validation.unique'
                                    )
                                )
                            )
                        ),
                        'fail'
                    );
                }

                // re add the client data to the template
                \Whsuite\Inputs\Post::set('Client', $client->toArray());
            }
        }

        $title = $this->lang->get('add_client');

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('client', new Client());

        $this->view->set('default_language_id', DEFAULT_LANG);

        $this->view->set('country_list', Country::getCountries());

        $this->view->display('clients/addClient.php');
    }

    public function clientProfile($id)
    {
        $client = Client::find($id);
        if (empty($client) || $client->guest_account == 1) {
            App::get('session')->setFlash('error', $this->lang->get('item_not_found'));
            return $this->redirect('admin-client');
        }

        $this->view->set('client', $client);
        $this->view->set('client_credit', \App\Libraries\Transactions::allClientCredits($client->id));
        \Whsuite\Inputs\Post::set('Client', $client->toArray());
        $this->view->set('country_list', Country::getCountries(true));

        $title = $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name;
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

        // Load client's products
        $active_services = ProductPurchase::where('client_id', '=', $client->id)->where('status', '=', '1')->count();

        $this->view->set('products', ProductPurchase::where('client_id', '=', $client->id)->orderBy('id', 'desc')->limit('15')->get());
        $this->view->set('total_products', ProductPurchase::where('client_id', '=', $client->id)->count());
        $this->view->set('active_products', $active_services);

        // Load client's invoices
        $invoices = Invoice::where('client_id', '=', $client->id)->orderBy('id', 'desc')->limit('15')->get();
        $this->view->set('invoices', $invoices);

        // Load client's transactions
        $transactions = Transaction::where('client_id', '=', $client->id)
            ->orderBy('created_at', 'desc')
            ->limit('15')
            ->with(array(
                'Currency', 'Gateway', 'Invoice'
            ))
            ->get();
        $this->view->set('transactions', $transactions);

        // Load the client's currency
        $this->view->set('currency', $client->Currency()->first());

        // Load the client's email log
        $this->view->set('emails', $client->ClientEmail()->limit(15)->orderBy('id', 'desc')->get());

        // Load the client's notes
        $this->view->set('notes', $client->ClientNote()->limit(15)->orderBy('id', 'desc')->get());

        // Load the clients credit card accounts
        $cc_accounts = ClientCc::getCcs($client->id);

        // Load the clients ach accounts
        $ach_accounts = ClientAch::getAchs($client->id);

        // We now want to merge the CC and ACH records.
        $cc_ach_accounts = array();

        if (count($cc_accounts) > 0) {
            foreach ($cc_accounts as $cc) {
                $cc_ach_accounts[] = array('type' => 'cc', 'data' => $cc);
            }
        }

        if (count($ach_accounts) > 0) {
            foreach ($ach_accounts as $ach) {
                $cc_ach_accounts[] = array('type' => 'ach', 'data' => $ach);
            }
        }

        $this->view->set('payment_accounts', $cc_ach_accounts);

        $this->view->display('clients/profile.php');
    }

    public function clientActivate($id)
    {
        $client = Client::find($id);

        if (!empty($client)) {
            $client->activated = '1';

            if ($client->save()) {
                App::get('session')->setFlash('success', $this->lang->get('client_successfully_activated'));
                return $this->redirect('admin-client-profile', ['id' => $client->id]);
            }
        }
        App::get('session')->setFlash('error', $this->lang->get('error_activating_client'));
        return $this->redirect('admin-client');
    }

    public function clientEdit($id)
    {
        $client = Client::find($id);
        if (empty($client) || !\Whsuite\Inputs\Post::Get()) {
            return $this->redirect('admin-client');
        }
        $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Client'), Client::$rules);

        if ($validator->fails()) {
            // The validation failed. Find out why and send the user back to the client profile page and show the error(s)
            App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        } elseif (! $client->validateCustomFields(false)) {
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        } else {
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
            $client->status = $client_data['status'];
            $client->language_id = $client_data['language_id'];
            $client->is_taxexempt = $client_data['is_taxexempt'];

            if (isset($client_data['password']) && $client_data['password'] !='') {
                $client->password = $client_data['password'];
            }

            if ($client->status == '1') {
                $client->activated = '1';
            }

            try {
                if ($client->save() && $client->saveCustomFields(false)) {
                    App::get('session')->setFlash('success', $this->lang->get('client_updated'));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('error_updating_client'));
                }

                // catch the sentry exceptions for validation
            } catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
                // no email
                App::get('session')->setFlash(
                    'error',
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
                App::get('session')->setFlash(
                    'error',
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

            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        }
    }

    public function clientProducts($id, $page = 0)
    {
        $client = Client::find($id);
        if (empty($client) || !\Whsuite\Inputs\Post::Get()) {
            return $this->redirect('admin-client');
        }

        $title = $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name;
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add($title, 'admin-client-profile', array('id' => $client->id));
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

    }

    public function emailPassword($id)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        $new_password = $client->getRandomString(App::get('configs')->get('settings.general.random_password_length'));

        $client->password = $new_password;
        $client->save();

        $data = array(
            'password' => $new_password
        );

        // Send the email, we wont be storing this in the email log though as it will contain a raw password!
        if (App::get('email')->sendTemplateToClient($id, 'new_client_password', $data, array(), true)) {
            App::get('session')->setFlash('success', $this->lang->get('new_password_sent_to_client'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_sending_password_to_client'));
        }
        return $this->redirect('admin-client-profile', ['id' => $client->id]);
    }

    public function loginAsClient($id)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        $auth = \App\Libraries\ClientAuth::auth();

        try {
            $auth->login($client, true);
        } catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            App::get('session')->setFlash('error', $this->lang->get('client_pending_activation'));
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        }

        return $this->redirect('client-home');
    }

    public function editCc($id, $cc_id)
    {
        $client = Client::find($id);
        $cc = ClientCc::getCc($cc_id);
        if (empty($client) || empty($cc) || $client->id != $cc->client_id) {
            return $this->redirect('admin-client');
        }

        $post_data = \Whsuite\Inputs\Post::get();
        if (isset($post_data['submit'])) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Cc'), ClientCc::$rules);

            if ($validator->fails()) {
                // The validation failed. Find out why and send the user back to the client profile page and show the error(s)
                \App\Libraries\Message::set(
                    $return_error = $this->lang->formatErrors(
                        json_encode($validator->messages())
                    ),
                    'fail'
                );

            } else {
                // Passed validation. Now lets update the data
                $data = \Whsuite\Inputs\Post::get('Cc'); // Set the data into a var to make things easier.

                // Because the model might return its own error, we're going to
                // do the true/false check slightly differently. If the returned
                // data is 'null' we instead dont try to show a message as it will
                // mean it's already been set by the model.
                $ccResult = ClientCc::saveCc($data, $cc->id, $client->id);

                if ($ccResult === true) {
                    App::get('session')->setFlash('success', $this->lang->get('credit_card_successfully_saved'));
                    return $this->redirect('admin-client-profile', ['id' => $client->id]);

                } elseif ($ccResult === false) {
                    \App\Libraries\Message::set(
                        $this->lang->get('error_saving_credit_card'),
                        'fail'
                    );
                }
            }
        }

        $cc_array = $cc->toArray();

        if ($cc_array['gateway_data'] != '') {
            // If we have some data in the gateway_data field it means that
            // previously we'd asked that this card be stored off-site. So
            // we need to 'tick' that box.
            $cc_array['offsite'] = '1';
        }

        \Whsuite\Inputs\Post::set('Cc', $cc_array);
        $title = $this->lang->get('edit_cc');
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

        $this->view->set('client', $client);
        $this->view->set('client_credit', \App\Libraries\Transactions::allClientCredits($client->id));
        $this->view->set('cc', $cc);

        $this->view->set('currencies', Currency::formattedList('id', 'code'));

        // Set the CC decrypt route as it'll be needed by the decryption helper.
        $this->view->set('decryptRoute', App::get('router')->generate('admin-clientcc-decrypt', array('id' => $client->id, 'cc_id' => $cc->id)));
        if (App::get('configs')->get('settings.sys_private_key_passphrase') !='') {
            $this->view->set('passphraseAuth', true);
        } else {
            $this->view->set('passphraseAuth', false);
        }

        $this->view->display('clients/editCc.php');
    }

    public function decryptCc($id, $cc_id)
    {
        $client = Client::find($id);
        $cc = ClientCc::getCc($cc_id);
        if (empty($client) || empty($cc) || $client->id != $cc->client_id) {
            return $this->redirect('admin-client');
        }

        $crypt = \App::get('security');
        return $crypt->requestData('rsa', $this->admin_user, 'ClientCc', $cc_id, 'account_number');
    }

    public function newCc($id)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        $post_data = \Whsuite\Inputs\Post::get();
        if (isset($post_data['submit'])) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Cc'), ClientCc::$rules);

            if ($validator->fails()) {
                // The validation failed. Find out why and send the user back and show the error(s)
                \App\Libraries\Message::set(
                    $return_error = $this->lang->formatErrors(
                        json_encode($validator->messages())
                    ),
                    'fail'
                );
            } else {
                // Passed validation. Now lets update the data
                $data = \Whsuite\Inputs\Post::get('Cc'); // Set the data into a var to make things easier.

                // Because the model might return its own error, we're going to
                // do the true/false check slightly differently. If the returned
                // data is 'null' we instead dont try to show a message as it will
                // mean it's already been set by the model.
                $ccResult = ClientCc::saveCc($data, 0, $client->id);

                if ($ccResult === true) {
                    App::get('session')->setFlash('success', $this->lang->get('credit_card_successfully_saved'));
                    return $this->redirect('admin-client-profile', ['id' => $client->id]);

                } elseif ($ccResult == false) {
                    \App\Libraries\Message::set(
                        $this->lang->get('error_saving_credit_card'),
                        'fail'
                    );
                }
            }
        }

        $title = $this->lang->get('new_cc');
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('cc', array());

        $this->view->set('client', $client);
        $this->view->set('client_credit', \App\Libraries\Transactions::allClientCredits($client->id));

        // For the gateway list we're setting the option to have a 'zero row'
        // as false as we want to do it manually togive it a different name.
        $this->view->set('gateway_list', Gateway::getGateways());
        $this->view->set('currencies', Currency::formattedList('id', 'code'));


        $this->view->display('clients/newCc.php');
    }

    public function deleteCc($id, $cc_id)
    {
        $client = Client::find($id);
        $cc = ClientCc::getCc($cc_id);
        if (empty($client) || empty($cc) || $client->id != $cc->client_id) {
            return $this->redirect('admin-client');
        }

        if ($cc->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('credit_card_successfully_deleted'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_deleting_credit_card'));
        }
        return $this->redirect('admin-client-profile', ['id' => $client->id]);
    }

    public function editAch($id, $ach_id)
    {
        $client = Client::find($id);
        $ach = ClientAch::getAch($ach_id);

        if (empty($client) || empty($ach) || $client->id != $ach->client_id) {
            return $this->redirect('admin-client');
        }

        $post_data = \Whsuite\Inputs\Post::get();
        if (isset($post_data['submit'])) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Ach'), ClientAch::$rules);

            if ($validator->fails()) {
                // The validation failed. Find out why and send the user back to the client profile page and show the error(s)
                \App\Libraries\Message::set(
                    $return_error = $this->lang->formatErrors(
                        json_encode($validator->messages())
                    ),
                    'fail'
                );

            } else {
                // Passed validation. Now lets update the data
                $data = \Whsuite\Inputs\Post::get('Ach'); // Set the data into a var to make things easier.

                // Because the model might return its own error, we're going to
                // do the true/false check slightly differently. If the returned
                // data is 'null' we instead dont try to show a message as it will
                // mean it's already been set by the model.
                $achResult = ClientAch::saveAch($data, $ach->id, $client->id);

                if ($achResult === true) {
                    App::get('session')->setFlash('success', $this->lang->get('ach_account_successfully_saved'));
                    return $this->redirect('admin-client-profile', ['id' => $client->id]);

                } elseif ($achResult === false) {
                    \App\Libraries\Message::set(
                        $this->lang->get('error_saving_ach_account'),
                        'fail'
                    );
                }
            }
        }

        $ach_array = $ach->toArray();

        if ($ach_array['gateway_data'] != '') {
            // If we have some data in the gateway_data field it means that
            // previously we'd asked that this account be stored off-site. So
            // we need to 'tick' that box.
            $ach_array['offsite'] = '1';
        }

        \Whsuite\Inputs\Post::set('Ach', $ach_array);
        $title = $this->lang->get('edit_ach');
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

        $this->view->set('client', $client);
        $this->view->set('client_credit', \App\Libraries\Transactions::allClientCredits($client->id));
        $this->view->set('ach', $ach);
        $this->view->set('account_types', ClientAch::accountTypes());

        $this->view->set('gateway_list', Gateway::getGateways());

        if (App::get('configs')->get('settings.sys_private_key_passphrase') !='') {
            $this->view->set('passphraseAuth', true);
        } else {
            $this->view->set('passphraseAuth', false);
        }

        $this->view->display('clients/editAch.php');
    }

    /**
     * decrypt account number
     */
    public function decryptAch($id, $ach_id)
    {
        $client = Client::find($id);
        $ach = ClientAch::getAch($ach_id);
        if (empty($client) || empty($ach) || $client->id != $ach->client_id) {
            return $this->redirect('admin-client');
        }

        $crypt = \App::get('security');
        return $crypt->requestData('rsa', $this->admin_user, 'ClientAch', $ach_id, 'account_number');
    }

    /**
     * decrypt routing number
     */
    public function decryptAchRouting($id, $ach_id)
    {
        $client = Client::find($id);
        $ach = ClientAch::getAch($ach_id);
        if (empty($client) || empty($ach) || $client->id != $ach->client_id) {
            return $this->redirect('admin-client');
        }

        $crypt = \App::get('security');
        return $crypt->requestData('rsa', $this->admin_user, 'ClientAch', $ach_id, 'account_routing_number');
    }

    public function newAch($id)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        $post_data = \Whsuite\Inputs\Post::get();
        if (isset($post_data['submit'])) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Ach'), ClientAch::$rules);

            if ($validator->fails()) {
                // The validation failed. Find out why and send the user back to the client profile page and show the error(s)
                \App\Libraries\Message::set(
                    $return_error = $this->lang->formatErrors(
                        json_encode($validator->messages())
                    ),
                    'fail'
                );

            } else {
                // Passed validation. Now lets update the data
                $data = \Whsuite\Inputs\Post::get('Ach'); // Set the data into a var to make things easier.

                // Because the model might return its own error, we're going to
                // do the true/false check slightly differently. If the returned
                // data is 'null' we instead dont try to show a message as it will
                // mean it's already been set by the model.
                $achResult = ClientAch::saveAch($data, 0, $client->id);

                if ($achResult === true) {
                    App::get('session')->setFlash('success', $this->lang->get('ach_account_successfully_saved'));
                    return $this->redirect('admin-client-profile', ['id' => $client->id]);
                } elseif ($achResult === false) {
                    \App\Libraries\Message::set(
                        $this->lang->get('error_saving_ach_account'),
                        'fail'
                    );
                }
            }
        }

        $title = $this->lang->get('new_ach');
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('ach', array());
        $this->view->set('account_types', ClientAch::accountTypes());

        $this->view->set('client', $client);
        $this->view->set('client_credit', \App\Libraries\Transactions::allClientCredits($client->id));

        // For the gateway list we're setting the option to have a 'zero row'
        // as false as we want to do it manually togive it a different name.
        $this->view->set('gateway_list', Gateway::getGateways());
        $this->view->set('currencies', Currency::formattedList('id', 'code'));

        $this->view->display('clients/newAch.php');
    }

    public function deleteAch($id, $ach_id)
    {
        $client = Client::find($id);
        $ach = ClientAch::getAch($ach_id);
        if (empty($client) || empty($ach) || $client->id != $ach->client_id) {
            return $this->redirect('admin-client');
        }

        if ($ach->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('ach_account_successfully_deleted'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_deleting_ach_account'));
        }
        return $this->redirect('admin-client-profile', ['id' => $client->id]);
    }
}
