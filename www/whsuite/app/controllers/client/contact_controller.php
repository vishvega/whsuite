<?php

/**
 * Client Contact Controller
 *
 * Allows clients to manage their domain contact profiles.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2014, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class ContactController extends ClientController
{

    public function listContacts($page = 0)
    {
        if (!$this->logged_in) {
            //return header("Location: ".App::get('router')->generate('client-home'));
        }

        $conditions = array(
            array(
                'type' => 'where',
                'column' => 'client_id',
                'operator' => '=',
                'value' => $this->client->id
            )
        );

        $contacts = Contact::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'created_at', 'desc', 'client-contacts-paging');

        $title = $this->lang->get('domain_contacts');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('contacts', $contacts);

        return $this->view->display('contacts/listContacts.php');
    }

    public function manageContact($id)
    {
        if($this->logged_in) {
            // Check that the contact exists, and that it belongs to this client.
            $contact = Contact::find($id);

            // TODO: Convert this to non-hard coded
            $contact_titles = array(
                'Mr' => 'Mr',
                'Mrs' => 'Mrs',
                'Miss' => 'Miss',
                'Ms' => 'Ms'
            );

            if (!empty($contact) && $contact->client_id === $this->client->id) {

                // The contact exists, and belongs to this client. We're good to go!
                $data = \Whsuite\Inputs\Post::get();

                if (isset($data) && ! empty($data)) {

                    // We don't want people to be able to manually modify the contact
                    // type once it's been set!
                    $data['Contact']['contact_type'] = $contact->contact_type;

                    $validator = $this->validator->make($data['Contact'], Contact::$rules);
                    if (!$validator->fails()) {
                        // Validation passed

                        // Passed validation. Now lets update the data
                        $data = $data['Contact']; // Set the data into a var to make things easier.


                        if (App::get('domainhelper')->updateContact($data, $id, $this->client->id)) {
                            App::get('session')->setFlash('success', $this->lang->get('contact_profile_saved'));
                        } else {

                            App::get('session')->setFlash('error', $this->lang->get('error_saving_contact_profile'));
                        }
                        header("Location: ".App::get('router')->generate('client-contacts'));
                    } else {
                        \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'error');
                    }
                }

                \Whsuite\Inputs\Post::set('Contact', $contact->toArray());

                $title = $this->lang->get('manage_contact_profile');
                $this->view->set('title', $title);

                App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
                App::get('breadcrumbs')->add($this->lang->get('domain_contacts'), 'client-contacts');
                App::get('breadcrumbs')->add($title);
                App::get('breadcrumbs')->build();

                $this->view->set('contact_titles', $contact_titles);

                $this->view->set('country_list', Country::getCountries());
                $this->view->set('contact', $contact);
                return $this->view->display('contacts/contactForm.php');

            }
        }
        return header("Location: ".App::get('router')->generate('client-home'));
    }

    public function addContact()
    {
        if($this->logged_in) {

            $data = \Whsuite\Inputs\Post::get();

            $contact = new Contact();

            // TODO: Convert this to non-hard coded
            $contact_titles = array(
                'Mr',
                'Mrs',
                'Miss',
                'Ms'
            );

            $contact_types = array(
                'registrant' => $this->lang->get('registrant'),
                'administrative' => $this->lang->get('administrative'),
                'technical' => $this->lang->get('technical'),
                'billing' => $this->lang->get('billing')
            );

            if (isset($data) && ! empty($data)) {

                $data = $data['Contact'];

                $validator = $this->validator->make($data, Contact::$rules);

                if (!$validator->fails()) {
                    // Validation passed

                    // Populate the contact object.
                    $contact->client_id = $this->client->id;
                    $contact->contact_type = $data['contact_type'];
                    $contact->title = $data['title'];
                    $contact->first_name = $data['first_name'];
                    $contact->last_name = $data['last_name'];
                    $contact->email = $data['email'];
                    $contact->company = $data['company'];
                    $contact->job_title = $data['job_title'];
                    $contact->address1 = $data['address1'];
                    $contact->address2 = $data['address2'];
                    $contact->address3 = $data['address3'];
                    $contact->city = $data['city'];
                    $contact->state = $data['state'];
                    $contact->postcode = $data['postcode'];
                    $contact->country = $data['country'];
                    $contact->phone_cc = $data['phone_cc'];
                    $contact->phone = $data['phone'];
                    $contact->fax_cc = $data['fax_cc'];
                    $contact->fax = $data['fax'];

                    // Passed validation. Now lets update the data

                    if ($contact->save()) {
                        App::get('session')->setFlash('success', $this->lang->get('contact_profile_saved'));
                    } else {
                        App::get('session')->setFlash('error', $this->lang->get('error_saving_contact_profile'));
                    }
                } else {
                    App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
                }
            } else {

                \Whsuite\Inputs\Post::set('Contact', $contact->toArray());

                $title = $this->lang->get('add_contact_profile');
                $this->view->set('title', $title);

                App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
                App::get('breadcrumbs')->add($this->lang->get('domain_contacts'), 'client-contacts');
                App::get('breadcrumbs')->add($title);
                App::get('breadcrumbs')->build();

                $this->view->set('contact_titles', $contact_titles);
                $this->view->set('contact_types', $contact_types);

                $this->view->set('country_list', Country::getCountries());
                $this->view->set('contact', $contact);
                return $this->view->display('contacts/contactForm.php');
            }
        }
        return header("Location: ".App::get('router')->generate('client-contacts'));
    }

    public function deleteContact($id)
    {
        if($this->logged_in) {
            $contact = Contact::find($id);

            if(!empty($contact) && $contact->client_id === $this->client->id) {
                if($contact->delete()) {
                    App::get('session')->setFlash('success', $this->lang->get('contact_profile_deleted'));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('error_deleting_contact_profile'));
                }
            }
        }
        return header("Location: ".App::get('router')->generate('client-contacts'));
    }


}