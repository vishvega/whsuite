<?php

use \Symfony\Component\Translation\Translator as Translator;
use \Illuminate\Validation\Factory as Validation;

/**
 * Admin Login Controller
 *
 * Handles login and related methods for admins. Includes forgotten passwords,
 * logout, etc.
 */
class LoginController extends AdminController
{
    public $allow = array('index', 'forgottenPassword', 'resetPassword');

    /**
     * Index (Login form)
     *
     * Provides a login form for admins.
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
                $error = $this->lang->formatErrors($validator->messages());

            } else {
                // Validation passed, so lets load up the throttle data for this user.

                try {
                    $user_info = $this->admin_auth
                        ->getUserProvider()
                        ->findByLogin(\Whsuite\Inputs\Post::get('email'));

                    $throttle = $this->admin_throttle->findByUserId($user_info->id);

                } catch (Exception $e) {
                    $error = $this->lang->get('login_incorrect_details');
                }

                if (! isset($error)) {
                    try {
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
                            $error = $this->lang->get('login_account_suspended');
                        } elseif ($throttle->isBanned()) {
                            $error = $this->lang->get('login_account_banned');
                        } else {
                            // All good? Authenticate the user then.
                            $user = $this->admin_auth->authenticate($credentials, $remember);
                        }

                    } catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
                        $error = $this->lang->get('login_email_required');
                    } catch (Cartalyst\Sentry\Users\PasswordRequiredException $e) {
                        $throttle->addLoginAttempt();
                        $error = $this->lang->get('login_password_required');
                    } catch (Cartalyst\Sentry\Users\WrongPasswordException $e) {
                        $throttle->addLoginAttempt();
                        $error = $this->lang->get('login_incorrect_details');
                    } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                        $error = $this->lang->get('login_incorrect_details');
                    } catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {
                        $throttle->addLoginAttempt();
                        $error = $this->lang->get('login_account_not_active');
                    } catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
                        $throttle->addLoginAttempt();
                        $error = $this->lang->get('login_account_suspended');
                    } catch (Cartalyst\Sentry\Throttling\UserBannedException $e) {
                        $throttle->addLoginAttempt();
                        $error = $this->lang->get('login_account_banned');
                    }
                }
            }

            if (! isset($error)) {
                // No errors, direct them to the admin page

                // first regenerate the csrf token
                \App::get('session')->getCsrfToken()->regenerateValue();

                return $this->redirect('admin-home');
            }

            // If the script gets to this point, the user was not logged in.

            \App\Libraries\Message::set($error, 'fail'); // Assign any errors to the view
        }

        if (App::get('session')->hasFlash('success')) {
            \App\Libraries\Message::set(App::get('session')->getFlash('success'), 'success');
        }

        if (App::get('session')->hasFlash('error')) {
            \App\Libraries\Message::set(App::get('session')->getFlash('error'), 'fail');
        }

        $this->view->display('login/login.php'); // Show the login form
    }

    /**
     * Logout
     *
     * Logs the user out and redirects to the login page.
     */
    public function logout()
    {
        \App::get('session')->getCsrfToken()->regenerateValue();
        $this->admin_auth->logout();
        return $this->redirect('admin-login');
    }

    /**
     * Forgotten Password
     *
     * Temporary content for testing - to be refactored.
     */
    public function forgottenPassword()
    {
        $post_data = \Whsuite\Inputs\Post::get();

        if (isset($post_data['submit'])) {
            $rules = array(
                'email' => array('required', 'email')
            );
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get(), $rules);

            if ($validator->fails()) {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            } else {
                $email = \Whsuite\Inputs\Post::get('email');
                try {
                    $user = $this->admin_auth->getUserProvider()->findByLogin($email);
                    $reset_code = $user->getResetPasswordCode();

                    $reset_url = \App::get('router')->fullUrlGenerate(
                        'admin-reset-password',
                        array('user_id' => $user->id, 'reset_key' => $reset_code)
                    );

                    $data = array(
                        'staff' => $user,
                        'reset_url' => $reset_url
                    );

                    if (App::get('email')->sendTemplateToStaff($user->id, 'staff_password_reset_request', $data, array(), true)) {
                        App::get('session')->setFlash('success', $this->lang->get('password_reset_request_sent'));
                    } else {
                        App::get('session')->setFlash('error', $this->lang->get('password_reset_request_error'));
                    }

                    return $this->redirect('admin-login');
                } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
                    \App\Libraries\Message::set($this->lang->get('no_user_found'), 'fail');
                }
            }
        }
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
            $user = $this->admin_auth->getUserProvider()->findById($user_id);

            if ($user->checkResetPasswordCode($reset_key)) {
                $new_password = App::get('str')->random(16);

                if ($user->attemptResetPassword($reset_key, $new_password)) {
                    $this->view->set('password', $new_password);

                    $data = array(
                        'staff' => $user,
                        'password' => $new_password
                    );

                    $user->reset_password_code = null;
                    $user->save();

                    if (App::get('email')->sendTemplateToStaff($user->id, 'new_staff_password', $data, array(), true)) {
                        App::get('session')->setFlash('success', $this->lang->get('new_password_email_sent'));
                    } else {
                        App::get('session')->setFlash('error', $this->lang->get('new_password_email_error'));
                    }
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('new_password_invalid_reset_code'));
                }
            } else {
                App::get('session')->setFlash('error', $this->lang->get('new_password_invalid_reset_code'));
            }
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            App::get('session')->setFlash('error', $this->lang->get('new_password_invalid_reset_code'));
        }

        return $this->redirect('admin-login');
    }
}
