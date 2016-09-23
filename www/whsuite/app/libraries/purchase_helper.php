<?php

namespace App\Libraries;

use \Illuminate\Support\Str;

class PurchaseHelper
{
    public $validator;

    public function processPurchase($purchase_id)
    {
        $validator = new \Whsuite\Validator\Validator();
        $this->validator = $validator->init(DEFAULT_LANG);

        $purchase = \ProductPurchase::find($purchase_id);
        $product = $purchase->Product()->first();
        $product_type = $product->ProductType()->first();
        $client = $purchase->Client()->first();

        $email_template = \EmailTemplate::find($product->email_template_id);

        $security = \App::get('security');
        $email = \App::get('email');
        $lang = \App::get('translation');

        // Determin if this is a new purchase, of if its being renewed/extended.
        $is_new = false;
        if ($purchase->status == \ProductPurchase::PENDING) {
            $is_new = true;
        }

        if ($product_type->is_domain == '1') {
            $domain = $purchase->Domain->first();
            $registrar = $domain->Registrar()->first();
            $addon = $registrar->addon()->first();

            if (!empty($addon) && $purchase->status == \ProductPurchase::SUSPENDED) {
                $addon_cameled = Str::studly($addon->directory);
                \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled)
                    ->unsuspendService($domain->id);

                $purchase = $this->activatePurchaseStatus($purchase);
            }

            if (! empty($domain)) {
                $registrar = $domain->Registrar()->first();
                $addon = $registrar->addon()->first();

                $domain_data = json_decode($domain->registrar_data);

                $billing_period = \DomainPricing::find($domain_data->billing_period);
                if ($is_new) {
                    $CarbonRenewal = \Carbon\Carbon::now(
                        \App::get('configs')->get('settings.localization.timezone')
                    );
                    $CarbonRenewal->addYears($billing_period->years);
                    $next_renewal = $CarbonRenewal->toDateString();

                    $CarbonNext = $CarbonRenewal->copy();
                    $CarbonNext->subDays(\App::get('configs')->get('settings.billing.invoice_days'));
                    $next_invoice = $CarbonNext->toDateString();
                } else {
                    $CarbonRenewal = \Carbon\Carbon::parse(
                        $domain->date_expires,
                        \App::get('configs')->get('settings.localization.timezone')
                    );
                    $CarbonRenewal->addYears($billing_period->years);
                    $next_renewal = $CarbonRenewal->toDateString();

                    $CarbonNext = $CarbonRenewal->copy();
                    $CarbonNext->subDays(\App::get('configs')->get('settings.billing.invoice_days'));
                    $next_invoice = $CarbonNext->toDateString();
                }

                if (! empty($addon)) {
                    // An addon exists for this registrar.

                    if (isset($domain_data->action) && ($domain_data->action == 'transfer' || $domain_data->action == 'register')) {
                        // check through the domain contact details to see if we
                        // need to create new contact records before proceeding
                        // with the registration request.

                        if (isset($domain_data->registrant_contact)) {
                            $registrant_contact_id = $domain_data->registrant_contact;
                        }

                        if (isset($domain_data->administrative_contact)) {
                            $administrative_contact_id = $domain_data->administrative_contact;
                        }

                        if (isset($domain_data->technical_contact)) {
                            $technical_contact_id = $domain_data->technical_contact;
                        }

                        if (isset($domain_data->billing_contact)) {
                            $billing_contact_id = $domain_data->billing_contact;
                        }

                        // Check if registrant contact id is valid
                        if (isset($registrant_contact_id) && $registrant_contact_id > 0) {
                            $registrant_contact = \Contact::find($registrant_contact_id);

                            if (! $registrant_contact || $registrant_contact->client_id != $client->id) {
                                return false;
                            }
                        } else {
                            // Create a new registrant contact
                            $data = $domain_data->Registrant;

                            $data->contact_type = 'registrant';

                            $validator = $this->validator->make((array)$data, \Contact::$rules);

                            if ($validator->fails()) {
                                return false;
                            }

                            $contact = new \Contact();
                            $contact->client_id = $client->id;
                            $contact->contact_type = $data->contact_type;
                            $contact->title = $data->title;
                            $contact->first_name = $data->first_name;
                            $contact->last_name = $data->last_name;
                            $contact->email = $data->email;
                            $contact->company = $data->company;
                            $contact->job_title = $data->job_title;
                            $contact->address1 = $data->address1;
                            $contact->address2 = $data->address2;
                            $contact->address3 = $data->address3;
                            $contact->city = $data->city;
                            $contact->state = $data->state;
                            $contact->postcode = $data->postcode;
                            $contact->country = $data->country;
                            $contact->phone_cc = $data->phone_cc;
                            $contact->phone = $data->phone;
                            $contact->fax_cc = $data->fax_cc;
                            $contact->fax = $data->fax;

                            $contact->save();

                            // Set the new registrant contact id
                            $registrant_contact_id = $contact->id;
                        }

                        // Check if administrative contact id is valid
                        if (isset($administrative_contact_id) && $administrative_contact_id > 0) {
                            $administrative_contact = \Contact::find($administrative_contact_id);

                            if (! $administrative_contact || $administrative_contact->client_id != $client->id) {
                                return false;
                            }
                        } else {
                            // Create a new administrative contact
                            $data = $domain_data->Administrative;

                            $data->contact_type = 'administrative';

                            $validator = $this->validator->make((array)$data, \Contact::$rules);

                            if ($validator->fails()) {
                                return false;
                            }

                            $contact = new \Contact();
                            $contact->client_id = $client->id;
                            $contact->contact_type = $data->contact_type;
                            $contact->title = $data->title;
                            $contact->first_name = $data->first_name;
                            $contact->last_name = $data->last_name;
                            $contact->email = $data->email;
                            $contact->company = $data->company;
                            $contact->job_title = $data->job_title;
                            $contact->address1 = $data->address1;
                            $contact->address2 = $data->address2;
                            $contact->address3 = $data->address3;
                            $contact->city = $data->city;
                            $contact->state = $data->state;
                            $contact->postcode = $data->postcode;
                            $contact->country = $data->country;
                            $contact->phone_cc = $data->phone_cc;
                            $contact->phone = $data->phone;
                            $contact->fax_cc = $data->fax_cc;
                            $contact->fax = $data->fax;

                            $contact->save();

                            // Set the new administrative contact id
                            $administrative_contact_id = $contact->id;
                        }

                        // Check if technical contact id is valid
                        if (isset($technical_contact_id) && $technical_contact_id > 0) {
                            $technical_contact = \Contact::find($technical_contact_id);

                            if (! $technical_contact || $technical_contact->client_id != $client->id) {
                                return false;
                            }
                        } else {
                            // Create a new technical contact
                            $data = $domain_data->Technical;

                            $data->contact_type = 'technical';

                            $validator = $this->validator->make((array)$data, \Contact::$rules);

                            if ($validator->fails()) {
                                return false;
                            }

                            $contact = new \Contact();
                            $contact->client_id = $client->id;
                            $contact->contact_type = $data->contact_type;
                            $contact->title = $data->title;
                            $contact->first_name = $data->first_name;
                            $contact->last_name = $data->last_name;
                            $contact->email = $data->email;
                            $contact->company = $data->company;
                            $contact->job_title = $data->job_title;
                            $contact->address1 = $data->address1;
                            $contact->address2 = $data->address2;
                            $contact->address3 = $data->address3;
                            $contact->city = $data->city;
                            $contact->state = $data->state;
                            $contact->postcode = $data->postcode;
                            $contact->country = $data->country;
                            $contact->phone_cc = $data->phone_cc;
                            $contact->phone = $data->phone;
                            $contact->fax_cc = $data->fax_cc;
                            $contact->fax = $data->fax;

                            $contact->save();

                            // Set the new technical contact id
                            $technical_contact_id = $contact->id;
                        }

                        // Check if billing contact id is valid
                        if (isset($billing_contact_id) && $billing_contact_id > 0) {
                            $billing_contact = \Contact::find($billing_contact_id);

                            if (! $billing_contact || $billing_contact->client_id != $client->id) {
                                return false;
                            }
                        } else {
                            // Create a new technical contact
                            $data = $domain_data->Billing;

                            $data->contact_type = 'billing';

                            $validator = $this->validator->make((array)$data, \Contact::$rules);

                            if ($validator->fails()) {
                                return false;
                            }

                            $contact = new \Contact();
                            $contact->client_id = $client->id;
                            $contact->contact_type = $data->contact_type;
                            $contact->title = $data->title;
                            $contact->first_name = $data->first_name;
                            $contact->last_name = $data->last_name;
                            $contact->email = $data->email;
                            $contact->company = $data->company;
                            $contact->job_title = $data->job_title;
                            $contact->address1 = $data->address1;
                            $contact->address2 = $data->address2;
                            $contact->address3 = $data->address3;
                            $contact->city = $data->city;
                            $contact->state = $data->state;
                            $contact->postcode = $data->postcode;
                            $contact->country = $data->country;
                            $contact->phone_cc = $data->phone_cc;
                            $contact->phone = $data->phone;
                            $contact->fax_cc = $data->fax_cc;
                            $contact->fax = $data->fax;

                            $contact->save();

                            // Set the new billing contact id
                            $billing_contact_id = $contact->id;
                        }

                        // Put the contact id's into an array for the domain helper
                        $contacts = array(
                            'registrant' => $registrant_contact_id,
                            'technical' => $technical_contact_id,
                            'administrative' => $administrative_contact_id,
                            'billing' => $billing_contact_id
                        );
                    }

                    if (isset($domain_data->action) && $domain_data->action == 'transfer') {
                        $domain_data = \App::factory('\App\Libraries\DomainHelper')
                            ->transferDomain($domain, $contacts, $auth_code);

                        $purchase->status = '1';

                        // Build Email Template
                        $email_data = array(
                            'client' => $client,
                            'purchase' => $purchase,
                            'domain' => $domain,
                            'product' => $product,
                            'next_renewal' => $next_renewal,
                            'next_invoice' => $next_invoice
                        );

                        $html = false;
                        if ($client->html_emails == '1') {
                            $html = true;
                        }

                        $email->sendTemplateToClient(
                            $client->id,
                            'domain_transfer_initiated',
                            $email_data
                        );

                    } elseif (isset($domain_data->action) && $domain_data->action == 'renew') {
                        $domain_data = \App::factory('\App\Libraries\DomainHelper')->renewDomain($domain, $billing_period->years);

                        $purchase->status = '1';

                        // Build Email Template
                        $email_data = array(
                            'client' => $client,
                            'purchase' => $purchase,
                            'domain' => $domain,
                            'product' => $product,
                            'next_renewal' => $next_renewal,
                            'next_invoice' => $next_invoice
                        );

                        $html = false;
                        if ($client->html_emails == '1') {
                            $html = true;
                        }

                        $email->sendTemplateToClient(
                            $client->id,
                            'domain_renewed',
                            $email_data
                        );
                    } elseif (isset($domain_data->action) && $domain_data->action == 'register') {
                        $nameservers = explode(", ", $domain->nameservers);

                        $domain_data = \App::factory('\App\Libraries\DomainHelper')->registerDomain($domain, $billing_period->years, $nameservers, $contacts, $custom_fields = array());

                        $purchase->status = '1';

                        // Build Email Template
                        if ($product->email_template_id > 0) {
                            $email_data = array(
                                'client' => $client,
                                'purchase' => $purchase,
                                'domain' => $domain,
                                'product' => $product,
                                'next_renewal' => $next_renewal,
                                'next_invoice' => $next_invoice
                            );

                            $html = false;
                            if ($client->html_emails == '1') {
                                $html = true;
                            }

                            $email->sendTemplateToClient(
                                $client->id,
                                $email_template->slug,
                                $email_data
                            );
                        }
                    } elseif (isset($domain_data->action) && $domain_data->action == 'restore') {
                        $domain_data = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.ucfirst($addon->directory))
                            ->restoreDomain($domain->id);

                        $purchase->status = '1';

                        // Build Email Template
                        $email_data = array(
                            'client' => $client,
                            'purchase' => $purchase,
                            'domain' => $domain,
                            'product' => $product,
                            'next_renewal' => $next_renewal,
                            'next_invoice' => $next_invoice
                        );

                        $html = false;
                        if ($client->html_emails == '1') {
                            $html = true;
                        }

                        $email->sendTemplateToClient(
                            $client->id,
                            'domain_restored',
                            $email_data
                        );
                    }
                }
            }

        } elseif ($product_type->is_hosting == '1') {
            $hosting = $purchase->Hosting()->first();

            if (! empty($hosting)) {
                $server = $hosting->Server()->first();
            }

            if (! empty($server)) {
                $server_group = $server->ServerGroup()->first();
            }

            if (! empty($server_group)) {
                $server_module = $server_group->ServerModule()->first();
            }

            if (! empty($server_module)) {
                $addon = $server_module->Addon()->first();
            }

            $server_helper = \App::factory('\App\Libraries\ServerHelper');
            $server_helper->initAddon($server->id);

            if (!empty($addon) && $purchase->status == \ProductPurchase::SUSPENDED) {
                $server_helper->unsuspendService($purchase, $hosting);

                $purchase = $this->activatePurchaseStatus($purchase);
            }

            $billing_period = $purchase->BillingPeriod()->first();

            if ($billing_period->days > 0) {
                $days = (int)$billing_period->days;

                if ($is_new) {
                    $CarbonRenewal = \Carbon\Carbon::now(
                        \App::get('configs')->get('settings.localization.timezone')
                    );
                } else {
                    $CarbonRenewal = \Carbon\Carbon::parse(
                        $purchase->next_renewal,
                        \App::get('configs')->get('settings.localization.timezone')
                    );
                }
                $CarbonRenewal->addDays($days);

                $next_renewal = $CarbonRenewal->toDateString();

                $CarbonRenewal->subDays(\App::get('configs')->get('settings.billing.invoice_days'));

                $next_invoice = $CarbonRenewal->toDateString();
            } else {
                $next_renewal = '0000-00-00';
                $next_invoice = '0000-00-00';
            }

            if (! empty($addon)) {
                if ($is_new) {
                    $server_helper = \App::factory('\App\Libraries\ServerHelper');
                    $server_helper->defaultServer($server_group);

                    $hosting_data = $server_helper->createService($purchase, $hosting);

                    if (! isset($hosting->domain) || $hosting->domain == '') {
                        $hosting->domain = $hosting_data['domain'];
                    }

                    if (! isset($hosting->nameservers) || $hosting->nameservers == '') {
                        $hosting->nameservers = $hosting_data['nameservers'];
                    }

                    if (! isset($hosting->diskspace_limit) || $hosting->diskspace_limit == '') {
                        $hosting->diskspace_limit = $hosting_data['diskspace_limit'];
                    }

                    if (! isset($hosting->diskspace_usage) || $hosting->diskspace_usage == '') {
                        $hosting->diskspace_usage = $hosting_data['diskspace_usage'];
                    }

                    if (! isset($hosting->bandwidth_limit) || $hosting->bandwidth_limit == '') {
                        $hosting->bandwidth_limit = $hosting_data['bandwidth_limit'];
                    }

                    if (! isset($hosting->bandwidth_usage) || $hosting->bandwidth_usage == '') {
                        $hosting->bandwidth_usage = $hosting_data['bandwidth_usage'];
                    }

                    if (! isset($hosting->status) || $hosting->status == '') {
                        $hosting->status = $hosting_data['status'];
                    }

                    if (! isset($hosting->username) || $hosting->username == '') {
                        $hosting->username = $hosting_data['username'];
                    }

                    if (isset($hosting_data['password']) && $hosting_data['password'] != '') {
                        $hosting->password = $security->encrypt($hosting_data['password']);
                    }

                    $hosting->save();

                    $purchase->status = '1';

                    // Use the decrypted password again as we need it for the email template.
                    $hosting->password = $hosting_data['password'];

                    // Build Email Template
                    if ((int) $product->email_template_id > 0) {
                        $email_data = array(
                            'client' => $client,
                            'purchase' => $purchase,
                            'hosting' => $hosting,
                            'product' => $product,
                            'next_renewal' => $next_renewal,
                            'next_invoice' => $next_invoice
                        );

                        $html = false;
                        if ($client->html_emails == '1') {
                            $html = true;
                        }

                        $email->sendTemplateToClient(
                            $client->id,
                            $email_template->slug,
                            $email_data
                        );
                    }

                } else {
                    $server_helper = \App::factory('\App\Libraries\ServerHelper');
                    $server_helper->initAddon($hosting->server_id);

                    $hosting_data = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.ucfirst($addon->directory))
                        ->renewService($purchase, $hosting);

                    if (is_array($hosting_data) && isset($hosting_data['status'])) {
                        $hosting->diskspace_limit = $hosting_data['diskspace_limit'];
                        $hosting->diskspace_usage = $hosting_data['diskspace_usage'];
                        $hosting->bandwidth_limit = $hosting_data['bandwidth_limit'];
                        $hosting->bandwidth_usage = $hosting_data['bandwidth_usage'];
                        $hosting->status = $hosting_data['status'];
                    } else {
                        $hosting->status = '1';
                    }

                    $hosting->save();

                    $purchase->status = '1';
                }
            }
        } elseif ($product_type->addon_id > 0) {
            // This purchased product is handled by an addon. Load up the addons
            // product type helper file so we can allow it to activate the order
            // and update the next renewal period date.
            $addon = \Addon::find($product_type->addon_id);

            $addon_details = $addon->details();
            $addon_cameled = Str::studly($addon->directory);

            // Load the addon product handler
            $product_helper = \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled);

            if (! empty($addon) && $purchase->status == \ProductPurchase::SUSPENDED) {
                if (method_exists($product_helper, 'unsuspendService')) {
                    $product_helper->unsuspendService($product->id);
                }

                $purchase = $this->activatePurchaseStatus($purchase);
            }

            $isCreate = false;

            if ($is_new) {
                $result = $product_helper->createService($purchase->id);

                $isCreate = true;
            } else {
                $result = $product_helper->renewService($purchase->id);
            }

            if (! isset($result['result']) || $result['result'] === false) {
                // Action failed, return a flash error from the addon if available.
                if (isset($result['flash'])) {
                    $error = $result['flash'];
                } else {
                    // Flash error missing, show a system one instead.
                    if ($isCreate) {
                        $error = $lang->get('error_creating_account');
                    } else {
                        $error = $lang->get('error_renewing_account');
                    }
                }

                if (isset($error) && ! empty($error)) {
                    \App::get('session')->setFlash('error', $lang->get($error));
                }
            } else {
                if (isset($result['flash']) && ! empty($result['flash'])) {
                    \App::get('session')->setFlash('success', $lang->get($result['flash']));
                }
            }

            // Get the next renewal date from the addon.
            $renewal_dates = $product_helper->getNextRenewalDate($purchase->id);

            if (isset($renewal_dates['next_renewal'])) {
                $next_renewal = $renewal_dates['next_renewal'];
            } else {
                $next_renewal = '0000-00-00';
            }

            if (isset($renewal_dates['next_invoice'])) {
                $next_invoice = $renewal_dates['next_invoice'];
            } else {
                $next_invoice = '0000-00-00';
            }

            $purchase->status = \ProductPurchase::ACTIVE;

            if ((int) $product->email_template_id > 0) {
                $email_data = array(
                    'client' => $client,
                    'purchase' => $purchase,
                    'product' => $product,
                    'next_renewal' => $next_renewal,
                    'next_invoice' => $next_invoice
                );

                $html = false;
                if ($client->html_emails == '1') {
                    $html = true;
                }

                $email->sendTemplateToClient(
                    $client->id,
                    $email_template->slug,
                    $email_data
                );
            }

        } else {
            $addon = $product->Addon()->first();

            $billing_period = $purchase->BillingPeriod()->first();
            if ($billing_period->days > 0) {
                if ($is_new) {
                    $CarbonRenewal = \Carbon\Carbon::now(
                        \App::get('configs')->get('settings.localization.timezone')
                    );
                } else {
                    $CarbonRenewal = \Carbon\Carbon::parse(
                        $purchase->next_renewal,
                        \App::get('configs')->get('settings.localization.timezone')
                    );
                }
                $CarbonRenewal->addDays($billing_period->days);

                $next_renewal = $CarbonRenewal->toDateString();

                $CarbonRenewal->subDays(\App::get('configs')->get('settings.billing.invoice_days'));

                $next_invoice = $CarbonRenewal->toDateString();
            } else {
                $next_renewal = '0000-00-00';
                $next_invoice = '0000-00-00';
            }

            if (! empty($addon)) {
                if ($is_new) {
                    \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.ucfirst($addon->directory))->createService($purchase->id);

                    $purchase->status = \ProductPurchase::ACTIVE;

                } else {
                    \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.ucfirst($addon->directory))->renewService($purchase->id);

                    $purchase->status = \ProductPurchase::ACTIVE;
                }

                if ((int) $product->email_template_id > 0) {
                    $email_data = array(
                        'client' => $client,
                        'purchase' => $purchase,
                        'product' => $product,
                        'next_renewal' => $next_renewal,
                        'next_invoice' => $next_invoice
                    );

                    $html = false;
                    if ($client->html_emails == '1') {
                        $html = true;
                    }

                    $email->sendTemplateToClient(
                        $client->id,
                        $email_template->slug,
                        $email_data
                    );
                }
            }
        }

        if ($is_new) {
            // Check for product addon purchases and activate those.
            $purchased_addons = $purchase->ProductAddonPurchase()->get();

            if ($purchased_addons->count() > 0) {
                foreach ($purchased_addons as $purchased_addon) {
                    $product_addon = $purchased_addon->ProductAddon()->first();

                    if ($purchased_addon->status == '0') {
                        $purchased_addon->status = '1';
                        $purchased_addon->save();

                        \App::get('hooks')->callListeners(
                            'purchased-addon-activated-'.$product_addon->addon_slug,
                            $purchased_addon_id
                        );
                        \App::get('hooks')->callListeners('purchased-addon-activated', $purchased_addon);
                    }
                }
            }
        }

        $purchase->next_renewal = $next_renewal;
        $purchase->next_invoice = $next_invoice;

        $purchase->save();

        if ($is_new && $product->stock > 0) {
            $product->stock = ($product->stock - 1);
            $product->save();
        }
    }

    public function terminatePurchase($id)
    {
        $purchase = \ProductPurchase::find($id);
        $product = $purchase->Product()->first();
        $product_type = $product->ProductType()->first();

        if ($product_type->is_domain == '1') {
            $registrar = $domain->Registrar()->first();
            $addon = $registrar->addon()->first();

            $domain = $purchase->Domain->first();

            if (! empty($addon)) {
                $addon_cameled = Str::studly($addon->directory);
                \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled)
                    ->terminateService($domain->id);
            }

        } elseif ($product_type->is_hosting == '1') {
            $hosting = $purchase->Hosting()->first();

            if (empty($hosting)) {
                // The hosting record does not exist, thus must have already been terminated.
                return true;
            }

            $server = $hosting->Server()->first();
            if (empty($server)) {
                // The server was forcefully removed. We have no ability to terminate this service.
                return false;
            }

            $server_group = $server->ServerGroup()->first();

            if (empty($server_group)) {
                // The server group was forcefully removed. We have no ability to terminate this service
                return false;
            }

            $server_module = $server_group->ServerModule()->first();
            if (empty($server_module)) {
                // A server module wasn't found. That means that WHSuite isn't actually handling the
                // service. We return true to allow the order to be terminate.
                return true;
            }
            $server_helper = \App::factory('\App\Libraries\ServerHelper');
            $server_helper->initAddon($server->id);

            if (! empty($addon)) {
                $server_helper->terminateService($purchase, $hosting);
            }

        } else {
            $addon = $product->Addon()->first();

            if (! empty($addon)) {
                $addon_cameled = Str::studly($addon->directory);
                $AddonHelper = \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled);

                if (method_exists($AddonHelper, 'terminateService')) {
                    $AddonHelper->terminateService($product->id);
                }
            }
        }

        $purchase->status = 3;
        $purchase->save();
    }

    /**
     * activate a purchase
     *
     * @param object $Purchase
     * @return object $Purchase
     */
    protected function activatePurchaseStatus($Purchase)
    {
        $Purchase->status = \ProductPurchase::ACTIVE;
        $Purchase->save();

        return $Purchase;
    }
}
