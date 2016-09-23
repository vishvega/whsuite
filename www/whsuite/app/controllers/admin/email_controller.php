<?php
/**
 * Email Admin Controller
 *
 * The email admin controller handles all email related admin methods.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class EmailController extends AdminController
{
    protected function indexToolbar()
    {
        $route = \App::get('dispatcher')->getRoute();
        return array(
            array(
                'url_route' => 'admin-client-profile',
                'link_class' => '',
                'icon' => 'fa fa-list-ul',
                'label' => 'manage_client',
                'route_params' => array(
                    'id' => $route->values['id']
                )
            ),
            array(
                'url_route' => 'admin-clientemail-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'compose_email',
                'route_params' => array(
                    'id' => $route->values['id']
                )
            )
        );
    }

    public function viewSentEmail($id, $email_id)
    {
        $email = ClientEmail::find($email_id);
        if (empty($email)) {
            return $this->redirect('admin-client');
        }

        $this->view->set('email', $email);
        $this->view->set('client', $email->Client()->first());

        $this->view->display('clients/emails/emailView.php');
    }

    public function viewEmailBody($id, $email_id)
    {
        $email = ClientEmail::find($email_id);
        if (empty($email)) {
            return $this->redirect('admin-client');
        }

        $this->view->set('email', $email);
        $this->view->set('client', $email->Client()->first());

        $this->view->display('clients/emails/emailViewBody.php');
    }

    public function clientEmails($id, $page = 1)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        $emails = ClientEmail::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, null, 'created_at', 'desc', 'admin-clientemail-paging', array('id' => $id));

        $title = $this->lang->get('email_log').' - '.$client->first_name.' '.$client->last_name;

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set(
            array(
                'title' => $title,
                'client' => $client,
                'client_credit' => \App\Libraries\Transactions::allClientCredits($client->id),
                'emails' => $emails,
                'toolbar' => $this->indexToolbar()
            )
        );

        $this->view->display('clients/emails/list.php');
    }

    public function composeEmail($id)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        $title = $this->lang->get('compose_email').' - '.$client->first_name.' '.$client->last_name;

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::geT('breadcrumbs')->add(
            $this->lang->get('email_log').' - '.$client->first_name.' '.$client->last_name,
            'admin-clientemail',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

        if (\Whsuite\Inputs\Post::get()) {
            $cc = \Whsuite\Inputs\Post::get('cc');
            // explode the list of CC'd email addreses if there is more than one.
            if (strpos($cc, ',') !== false) {
                $cc_emails = explode(',', $cc);
                $cc = array_map('trim', $cc_emails); // strips whitespaces from emails.
            }

            $bcc = \Whsuite\Inputs\Post::get('bcc');
            // explode the list of BCC'd email addreses if there is more than one.
            if (strpos($bcc, ',') !== false) {
                $bcc_emails = explode(',', $bcc);
                $bcc = array_map('trim', $bcc_emails); // strips whitespaces from emails.
            }

            $subject = \Whsuite\Inputs\Post::get('subject');

            // As default all posted data gets converted from html to it's character codes for
            // security. However in this instance, we need to convert it back.
            $body =  htmlspecialchars_decode(\Whsuite\Inputs\Post::get('email_body'));

            if (!$subject || $subject == '') {
                // No subject entered, throw an error.
                App::get('session')->setFlash('error', $this->lang->get('no_subject_entered'));
                return $this->redirect('admin-clientemail-add', ['id' => $client->id]);
            }

            if (!$body || $body == '') {
                // No body entered, throw an error.
                App::get('session')->setFlash('error', $this->lang->get('no_message_entered'));
                return $this->redirect('admin-clientemail-add', ['id' => $client->id]);
            }

            $html = false;
            if ($client->html_emails == '1') {
                $html = true;
            }

            $email_data = array(
                'client' => $client,
                'settings' => App::get('configs')->get('settings')
            );

            // All ok, now time to send the email, and insert a copy of it into the client's email log.
            if (App::get('email')->sendEmail($client->email, $subject, $body, $html, $email_data, $cc, $bcc)) {
                // all ok, now insert the log

                $emailLog = new ClientEmail;
                $emailLog->client_id = $client->id;
                $emailLog->subject = App::get('email')->parseData($subject, $email_data);
                $emailLog->body = App::get('email')->parseData($body, $email_data);
                $emailLog->to = $client->email;

                if (is_array($cc)) {
                    $emailLog->cc = implode(',', $cc);
                } else {
                    $emailLog->cc = $cc;
                }

                if (is_array($bcc)) {
                    $emailLog->bcc = implode(',', $bcc);
                } else {
                    $emailLog->bcc = $bcc;
                }
                $emailLog->save();

                // All done, time to redirect!
                App::get('session')->setFlash('success', $this->lang->get('email_sent'));
                return $this->redirect('admin-clientemail', ['id' => $client->id]);
            } else {
                App::get('session')->setFlash('error', $this->lang->get('email_sending_failed'));
                return $this->redirect('admin-clientemail', ['id' => $client->id]);
            }
        }

        $this->view->set('client', $client);
        $this->view->display('clients/emails/compose.php');
    }

    public function plaintextPreview($id)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        if (\Whsuite\Inputs\Post::get()) {
            $html = htmlspecialchars_decode(\Whsuite\Inputs\Post::get('email_body'));

            $converter = new \Markdownify;

            $data = array(
                'client' => $client
            );
            echo nl2br(App::get('email')->parseData($converter->parseString($html), $data));
        } else {
            return $this->redirect('admin-home');
        }
    }

    public function resendEmail($id, $email_id)
    {
        $client = Client::find($id);
        $email = ClientEmail::find($email_id);
        if (empty($client) || empty($email)) {
            return $this->redirect('admin-client');
        }

        $cc = $email->cc;
        // explode the list of CC'd email addreses if there is more than one.
        if (strpos($cc, ',') !== false) {
            $cc_emails = explode(',', $cc);
            $cc = array_map('trim', $cc_emails); // strips whitespaces from emails.
        }

        $bcc = $email->bcc;
        // explode the list of BCC'd email addreses if there is more than one.
        if (strpos($bcc, ',') !== false) {
            $bcc_emails = explode(',', $bcc);
            $bcc = array_map('trim', $bcc_emails); // strips whitespaces from emails.
        }

        $subject = $email->subject;

        // As default all posted data gets converted from html to it's character codes for
        // security. However in this instance, we need to convert it back.
        $body =  htmlspecialchars_decode($email->body);

        $html = false;
        if ($client->html_emails == '1') {
            $html = true;
        }

        $email_data = array(); // we dont actually need to add any data as it's already processed when we resend an email.

        // All ok, now time to send the email, and insert a copy of it into the client's email log.
        if (App::get('email')->sendEmail($client->email, $subject, $body, $html, $email_data, $cc, $bcc)) {
            // all ok, now insert the log

            $emailLog = new ClientEmail;
            $emailLog->client_id = $client->id;
            $emailLog->subject = $subject;
            $emailLog->body = $body;
            $emailLog->to = $client->email;

            if (is_array($cc)) {
                $emailLog->cc = implode(',', $cc);
            } else {
                $emailLog->cc = $cc;
            }

            if (is_array($bcc)) {
                $emailLog->bcc = implode(',', $bcc);
            } else {
                $emailLog->bcc = $bcc;
            }
            $emailLog->save();

            // All done, time to redirect!
            App::get('session')->setFlash('success', $this->lang->get('email_sent'));
            return $this->redirect('admin-clientemail', ['id' => $client->id]);
        } else {
            App::get('session')->setFlash('error', $this->lang->get('email_sending_failed'));
            return $this->redirect('admin-clientemail', ['id' => $client->id]);
        }
    }

    public function deleteEmail($id, $email_id)
    {
        $client = Client::find($id);
        $email = ClientEmail::find($email_id);
        if (empty($client) || empty($email)) {
            return $this->redirect('admin-client');
        }

        if ($email->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('email_deleted'));
            return $this->redirect('admin-clientemail', ['id' => $client->id]);
        } else {
            App::get('session')->setFlash('error', $this->lang->get('email_deleting_failed'));
            return $this->redirect('admin-clientemail', ['id' => $client->id]);
        }
    }
}
