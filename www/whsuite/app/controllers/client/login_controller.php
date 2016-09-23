<?php
/**
 * Client Login Controller
 *
 * Handles login and related methods for clients. Includes forgotten passwords,
 * logout, etc.
 */
class LoginController extends ClientController
{
    public $allow = array('index', 'forgottenPassword', 'resetPassword');

    /**
     * Index
     *
     * Handles client login actions.
     */
    public function index($page = 1, $per_page = null)
    {
        $post_data = \Whsuite\Inputs\Post::get();

        if (isset($post_data['submit'])) {
            $rules = array(
                'email' => array('required', 'email'),
                'password' => array('required')
            );

            $validator = $this->validator->make(\Whsuite\Inputs\Post::get(), $rules);

            if ($validator->fails()) {
                App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));

            } else {
                try {
                    $user_info = $this->client_auth->getUserProvider()->findByLogin(
                        \Whsuite\Inputs\Post::get('email')
                    );

                    // check for a "guest account" throw user not found exception if it is one
                    if (is_object($user_info) && $user_info->guest_account == 0) {
                        $throttle = $this->client_throttle->findByUserId($user_info->id);

                        $credentials = array(
                            'email' => \Whsuite\Inputs\Post::get('email'),
                            'password' => \Whsuite\Inputs\Post::get('password'),
                        );

                        $remember = false; // If remember doesnt get set to true it will fall back to false.
                        if (\Whsuite\Inputs\Post::get('remember') == '1') {
                            $remember = true; // The user ticked the remember me box, so lets be sure to remember them.
                        }

                        // Check to see if the user account is suspended or banned, if not run the authenticator.
                        if ($throttle->isSuspended()) {
                            App::get('session')->setFlash('error', $this->lang->get('login_account_suspended'));
                        } elseif ($throttle->isBanned()) {
                            App::get('session')->setFlash('error', $this->lang->get('login_account_banned'));
                        } else {
                            // All good? Authenticate the user then.
                            $user = $this->client_auth->authenticate($credentials, $remember);

                            // Transfer order session data if applicable
                            $order_helper = App::factory('\App\Libraries\OrderHelper');
                            $order_helper->transferOrder($user->id);
                        }
                    } else {
                        throw new \Cartalyst\Sentry\Users\UserNotFoundException;
                    }

                } catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
                    App::get('session')->setFlash('error', $this->lang->get('login_email_required'));
                } catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e) {
                    $throttle->addLoginAttempt();
                    App::get('session')->setFlash('error', $this->lang->get('login_password_required'));
                } catch (\Cartalyst\Sentry\Users\WrongPasswordException $e) {
                    $throttle->addLoginAttempt();
                    App::get('session')->setFlash('error', $this->lang->get('login_incorrect_details'));
                } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    App::get('session')->setFlash('error', $this->lang->get('login_incorrect_details'));
                } catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
                    $throttle->addLoginAttempt();
                    App::get('session')->setFlash('error', $this->lang->get('login_account_not_active'));
                } catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
                    $throttle->addLoginAttempt();
                    App::get('session')->setFlash('error', $this->lang->get('login_account_suspended'));
                } catch (\Cartalyst\Sentry\Throttling\UserBannedException $e) {
                    $throttle->addLoginAttempt();
                    App::get('session')->setFlash('error', $this->lang->get('login_account_banned'));
                }
            }

            \App::get('session')->getCsrfToken()->regenerateValue();

            if (\Whsuite\Inputs\Post::get('redirect_to')) {
                return header("Location: ".\Whsuite\Inputs\Post::get('redirect_to'));
            }
        }

        header("Location: ".App::get('router')->generate('client-home'));
    }

    /**
     * Logout
     *
     * Logs the user out and redirects to the login page.
     */
    public function logout()
    {
        \App::get('session')->getCsrfToken()->regenerateValue();

        $this->client_auth->logout();

        // Transfer order session data if applicable
        $order_helper = App::factory('\App\Libraries\OrderHelper');
        $order_helper->transferOrder(0);

        header("Location: ".App::get('router')->generate('client-home'));
    }

    /**
     * Forgotten Password
     *
     * Temporary content for testing - to be refactored.
     */
    public function forgottenPassword()
    {
        if ($this->logged_in) {
            header("Location: ".App::get('router')->generate('client-home'));
        }

        $post_data = \Whsuite\Inputs\Post::get();
        if (isset($post_data['submit'])) {
            $rules = array(
                'email' => array('required', 'email')
            );
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get(), $rules);

            if ($validator->fails()) {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
                header("Location: ".App::get('router')->generate('client-home'));
            } else {
                $email = \Whsuite\Inputs\Post::get('email');

                try {
                    $user = $this->client_auth->getUserProvider()->findByLogin($email);

                    if (is_object($user) && $user->guest_account == 0) {
                        $reset_code = $user->getResetPasswordCode();

                        $reset_url = \App::get('router')->fullUrlGenerate(
                            'client-reset-password',
                            array(
                                'id' => $user->id,
                                'reset_key' => $reset_code
                            )
                        );

                        $data = array(
                            'client' => $user,
                            'reset_url' => $reset_url
                        );

                        if (App::get('email')->sendTemplateToClient($user->id, 'client_password_reset_request', $data, array(), true)) {
                            App::get('session')->setFlash('success', $this->lang->get('password_reset_request_sent'));
                        } else {
                            App::get('session')->setFlash('error', $this->lang->get('password_reset_request_error'));
                        }

                        header("Location: ".App::get('router')->generate('client-home'));
                    } else {
                        throw new \Cartalyst\Sentry\Users\UserNotFoundException;
                    }

                } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    App::get('session')->setFlash('error', $this->lang->get('no_user_found'));
                    header("Location: ".App::get('router')->generate('client-forgot-password'));
                }
            }
        }

        App::get('breadcrumbs')->add($this->lang->get('home'), 'client-home');
        App::get('breadcrumbs')->add($this->lang->get('reset_password'));
        App::get('breadcrumbs')->build();

        $this->view->display('login/forgottenPassword.php');
    }

    /**
     * Reset Password
     *
     * The user is directed here from the email sent to them asking them to reset their password.
     *
     * @param  integer $user_id   User Id
     * @param  [type] $reset_key Users unique reset key
     */
    public function resetPassword($user_id, $reset_key)
    {
        try {
            $user = $this->client_auth->getUserProvider()->findById($user_id);

            if ($user->checkResetPasswordCode($reset_key)) {
                $new_password = App::get('str')->random(16);

                if ($user->attemptResetPassword($reset_key, $new_password)) {
                    $data = array(
                        'client' => $user,
                        'password' => $new_password
                    );

                    // Wipe reset code
                    $user->reset_password_code = null;
                    $user->save();

                    if (App::get('email')->sendTemplateToClient($user->id, 'new_client_password', $data, array(), true)) {
                        App::get('session')->setFlash('success', $this->lang->get('new_password_email_sent'));
                    } else {
                        App::get('session')->setFlash('success', $this->lang->get('new_password_email_error'));
                    }
                    header("Location: ".App::get('router')->generate('client-home'));
                } else {
                    App::get('session')->setFlash('success', $this->lang->get('new_password_invalid_reset_code'));
                    header("Location: ".App::get('router')->generate('client-home'));
                }
            } else {
                App::get('session')->setFlash('success', $this->lang->get('new_password_invalid_reset_code'));
                header("Location: ".App::get('router')->generate('client-home'));
            }
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            App::get('session')->setFlash('success', $this->lang->get('new_password_invalid_reset_code'));
            header("Location: ".App::get('router')->generate('client-home'));
        }
    }

    public function createAccount()
    {
        $client = new Client();

        if (\Whsuite\Inputs\Post::get()) {
            // Because this will be a new account, we need to modify the rules a bit here
            // as we need to enforce that they set a password. The default rules assume
            // that the changing of a password is optional, in this case, it is not.
            $rules = Client::$rules;
            $rules['password'] = 'same:confirm_password|min:8';

            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Client'), $rules);

            if ($validator->fails()) {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            } elseif (!$client->validateCustomFields()) {
                App::get('session')->setFlash('error', $this->lang->get('error_creating_account'));
                return header("Location: ".App::get('router')->generate('client-create-account'));
            } else {
                // Passed validation. Now lets update the client data
                $client_data = \Whsuite\Inputs\Post::get('Client'); // Set the client data into a var to make things easier.

                // do quick check for guest account
                $guest_client = Client::where('email', '=', $client_data['email'])
                    ->where('guest_account', '=', 1)
                    ->first();

                // we have a guest client account, use that so any support tickets will be visible
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
                $client->language_id = $client_data['language_id'];
                $client->currency_id = $client_data['currency_id'];
                $client->password = $client_data['password'];
                $client->status = 1;
                $client->activated = 1;
                $client->guest_account = 0;

                try {
                    if ($client->save() && $client->saveCustomFields()) {
                        $credentials = array(
                            'email' => $client_data['email'],
                            'password' => $client_data['password'],
                        );

                        $user = $this->client_auth->authenticate($credentials, false);

                        // Transfer order session data if applicable
                        $order_helper = App::factory('\App\Libraries\OrderHelper');
                        $order_helper->transferOrder($user->id);

                        if (\Whsuite\Inputs\Post::get('redirect_to')) {
                            return header("Location: ".\Whsuite\Inputs\Post::get('redirect_to'));
                        }

                        return header("Location: ".App::get('router')->generate('client-home'));
                    } else {
                        \App\Libraries\Message::set($this->lang->get('error_creating_account'), 'fail');
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

                \Whsuite\Inputs\Post::set('Client', $client->toArray());
            }
        }

        $title = $this->lang->get('create_account');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('home'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('country_list', Country::getCountries());
        $this->view->set('client', $client);

        $this->view->set('default_language_id', DEFAULT_LANG);

        $this->view->display('login/createAccount.php');
    }
}
