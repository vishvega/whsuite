<?php

namespace App\Libraries;

class DomainHelper
{

    /**
     * Get Domain Info
     *
     * Works out the registrar addon being used for a domain, and then returns
     * a basic set of information for the domain.
     *
     * @param  Domain $domain Domain data object.
     * @return Object The object of domain data, a specific error object or false if there was a failure to retrieve data.
     */
    public function getDomainInfo($domain)
    {
        // Only continue if the domain var is a valid domain record from the database.
        if (! empty($domain)) {
            // Load up related data
            $purchase = $domain->ProductPurchase()->first();
            $registrar = $domain->Registrar()->first();
            $addon = $registrar->addon()->first();
            $client = $purchase->Client()->first();

            // Build the request object that we'll shoot over to the registrar addon
            $request = new \stdClass();
            $request->domain = $domain;
            $request->purchase_data = $purchase;
            $request->client = $client;

            // Check the registrar addon is correctly installed, if it's not
            // there's not much point in proceeding!
            if (! \App::checkInstalledAddon($addon->directory)) {
                return false;
            }

            // Run the service loader hook
            \App::get('hooks')->callListeners('admin-load-service-'.$addon->directory, $purchase->id);

            // Request and return the data from the registrar addon. The addon can
            // return false if it want's, and we'll show a generic error, however
            // if the domain's not registered it should return an object with an
            // error message and status as error.
            try {
                $result = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->getDomainInfo($request);
            } catch (\Exception $e) {
                $result = false;
            }

            return $this->processResponse($result, true);
        }
        return false; // You fail.
    }

    public function getDomainNameservers($domain)
    {
        // Only continue if the domain var is a valid domain record from the database.
        if (! empty($domain)) {
            // Load up related data
            $purchase = $domain->ProductPurchase()->first();
            $registrar = $domain->Registrar()->first();
            $addon = $registrar->addon()->first();
            $client = $purchase->Client()->first();

            // Build the request object that we'll shoot over to the registrar addon
            $request = new \stdClass();
            $request->domain = $domain;
            $request->purchase_data = $purchase;
            $request->client = $client;

            // Check the registrar addon is correctly installed, if it's not
            // there's not much point in proceeding!
            if (! \App::checkInstalledAddon($addon->directory)) {
                return false;
            }

            try {
                $result = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->getDomainNameservers($request);
            } catch (\Exception $e) {
                $result = false;
            }

            return $this->processResponse($result);
        }
        return false; // You fail.
    }

    public function setDomainNameservers($domain, $nameservers)
    {
        // Only continue if the domain var is a valid domain record from the database.
        if (! empty($domain)) {
            // Load up related data
            $purchase = $domain->ProductPurchase()->first();
            $registrar = $domain->Registrar()->first();
            $addon = $registrar->addon()->first();
            $client = $purchase->Client()->first();

            // Build the request object that we'll shoot over to the registrar addon
            $request = new \stdClass();
            $request->domain = $domain;
            $request->nameservers = $nameservers;

            // Check the registrar addon is correctly installed, if it's not
            // there's not much point in proceeding!
            if (! \App::checkInstalledAddon($addon->directory)) {
                return false;
            }

            try {
                $result = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->setDomainNameservers($request);
            } catch (\Exception $e) {
                $result = false;
            }

            return $this->processResponse($result);
        }
        return false; // You fail.
    }

    public function domainAvailability($domain_name)
    {
        $domain_parts = $this->splitDomain($domain_name);

        $sld = $domain_parts[0];
        $tld = '.'.$domain_parts[1];
        // Check to see which registrar handles this extension.
        $extension = \DomainExtension::where('extension', '=', $tld)->first();

        if ($extension) {
            $registrar = $extension->Registrar()->first();
            $addon = $registrar->Addon()->first();

            // Check the registrar addon is correctly installed, if it's not
            // there's not much point in proceeding!
            if (! \App::checkInstalledAddon($addon->directory)) {
                return false;
            }

            $request = new \stdClass();
            $request->domain = $domain_name;

            try {
                return $this->processResponse(\App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->domainAvailability($request));
            } catch (\Exception $e) {
                $result = new \stdClass();
                $result->availability = 'unknown';

                return $result;
            }
        }

        $result = new \stdClass();
        $result->availability = 'unknown';

        return $result;
    }

    public function registerDomain($domain, $years, $nameservers, $contacts, $custom_fields = array())
    {
        // Load up related data
        $purchase = $domain->ProductPurchase()->first();
        $registrar = $domain->Registrar()->first();
        $addon = $registrar->addon()->first();
        $client = $purchase->Client()->first();

        $request = new \stdClass();
        $request->domain = $domain;
        $request->years = $years;
        $request->nameservers = $nameservers;
        $request->contacts = $contacts;
        $request->custom_data = $this->setExtensionRegistrationParams($domain, $custom_fields);

        // Check the registrar addon is correctly installed, if it's not
        // there's not much point in proceeding!
        if (! \App::checkInstalledAddon($addon->directory)) {
            return false;
        }

        // Run the pre-registration hook
        \App::get('hooks')->callListeners('domain-pre-registration', $domain);

        // Request and return the data from the registrar addon. The addon can
        // return false if it want's, and we'll show a generic error, however
        // if the domain's not registered it should return an object with an
        // error message and status as error.

        $return = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->registerDomain($request);

        if ($return) {
            // Update the registrar data field.
            $registrar_data = array(
                'contacts' => $contacts
            );

            $domain->registrar_data = json_encode($registrar_data);
            $domain->save();

            // Run the post-registration hook
            \App::get('hooks')->callListeners('domain-post-registration', $domain);
        } else {
            // Run the registration failure hook
            \App::get('hooks')->callListeners('domain-registration-failed', $domain);
        }

        return $this->processResponse($return, false);
    }

    public function renewDomain($domain, $years)
    {
        // Load up related data
        $purchase = $domain->ProductPurchase()->first();
        $registrar = $domain->Registrar()->first();
        $addon = $registrar->addon()->first();
        $client = $purchase->Client()->first();

        $request = new \stdClass();
        $request->domain = $domain;
        $request->years = $years;

        // Check the registrar addon is correctly installed, if it's not
        // there's not much point in proceeding!
        if (! \App::checkInstalledAddon($addon->directory)) {
            return false;
        }

        // Run the pre-renewal hook
        \App::get('hooks')->callListeners('domain-pre-renewal', $domain);

        // Request and return the data from the registrar addon. The addon can
        // return false if it want's, and we'll show a generic error, however
        // if the domain's not registered it should return an object with an
        // error message and status as error.
        try {
            $return = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->renewDomain($request);

            if ($return->status == '1') {
                // Run the post-renewal hook
                \App::get('hooks')->callListeners('domain-post-renewal', $domain);
            } else {
                // Run the renewal failure hook
                \App::get('hooks')->callListeners('domain-renewal-failed', $domain);
            }
        } catch (\Exception $e) {
            $result = false;
        }

        return $this->processResponse($return);
    }

    public function transferDomain($domain, $contacts, $auth_code = null, $custom_fields = array())
    {
        // Load up related data
        $purchase = $domain->ProductPurchase()->first();
        $registrar = $domain->Registrar()->first();
        $addon = $registrar->addon()->first();
        $client = $purchase->Client()->first();

        $request = new \stdClass();
        $request->domain = $domain;
        $request->contacts = $contacts;
        $request->auth_code = $auth_code;
        $request->custom_data = $this->setExtensionTransferParams($domain, $custom_fields);

        // Check the registrar addon is correctly installed, if it's not
        // there's not much point in proceeding!
        if (! \App::checkInstalledAddon($addon->directory)) {
            return false;
        }

        // Run the pre-registration hook
        \App::get('hooks')->callListeners('domain-pre-transfer', $domain);

        // Request and return the data from the registrar addon. The addon can
        // return false if it want's, and we'll show a generic error, however
        // if the domain's not registered it should return an object with an
        // error message and status as error.
        try {
            $return = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->transferDomain($request);

            if ($return) {
                // Update the registrar data field.
                $registrar_data = array(
                    'contacts' => $contacts
                );

                $domain->registrar_data = json_encode($registrar_data);
                $domain->save();

                // Run the post-transfer hook
                \App::get('hooks')->callListeners('domain-post-transfer', $domain);
            } else {
                // Run the transfer failure hook
                \App::get('hooks')->callListeners('domain-transfer-failed', $domain);
            }
        } catch (\Exception $e) {
            $return = false;
        }

        return $this->processResponse($return);
    }

    public function setDomainLock($domain, $unlocked)
    {
        // Load up related data
        $purchase = $domain->ProductPurchase()->first();
        $registrar = $domain->Registrar()->first();
        $addon = $registrar->addon()->first();
        $client = $purchase->Client()->first();

        $request = new \stdClass();
        $request->domain = $domain;
        $request->unlocked = $unlocked;

        // Check the registrar addon is correctly installed, if it's not
        // there's not much point in proceeding!
        if (! \App::checkInstalledAddon($addon->directory)) {
            return false;
        }

        try {
            $return = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->setDomainLock($request);

            if ($return->status == '1') {
                // Run the post-renewal hook
                \App::get('hooks')->callListeners('domain-post-lock', $domain);
            } else {
                // Run the renewal failure hook
                \App::get('hooks')->callListeners('domain-lock-failed', $domain);
            }
        } catch (\Exception $e) {
            $return = false;
        }

        return $this->processResponse($return);
    }

    public function getDomainAuthCode($domain)
    {
        // Load up related data
        $purchase = $domain->ProductPurchase()->first();
        $registrar = $domain->Registrar()->first();
        $addon = $registrar->addon()->first();

        $request = new \stdClass();
        $request->domain = $domain;

        // Check the registrar addon is correctly installed, if it's not
        // there's not much point in proceeding!
        if (! \App::checkInstalledAddon($addon->directory)) {
            return false;
        }

        try {
            $return = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->getDomainAuthCode($request);

            if ($return->status == '1') {
                // Run the post-auth-code hook
                \App::get('hooks')->callListeners('domain-post-auth-code', $domain);
            } else {
                // Run the auth-code failure hook
                \App::get('hooks')->callListeners('domain-auth-code-failed', $domain);
            }
        } catch (\Exception $e) {
            $return = false;
        }

        return $this->processResponse($return);
    }

    public function getContact($contact_id)
    {
        return \Contact::where('id', '=', $contact_id)->first();
    }

    public function updateContact($data, $contact_id, $client_id)
    {
        // Load the contact
        $contact = \Contact::find($contact_id);

        // Check the contact exists and matches up to the client id supplied.
        if (!$contact || $contact->client_id != $client_id) {
            return false;
        }

        // Set the field values from the provided data array.
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

        if ($contact->save()) {
            // The contact was saved correctly. We now want to update all domains
            // that use this contact profile, so they use the latest contact details.
            // To do this we do a generic update on a domain contact ID using
            // another method.

            return $this->reloadContacts($contact->id);
        }

        // If the contact wasn't saved, return false and it'll sow the correct
        // error message to the user via the contact controller.
        return false;
    }

    /**
     * Reload Contacts
     *
     * After a contact record is updated we need to go through all domains that
     * use that contact, and force them to update their contact records with the
     * registries they are registered at. This method looks for all associated
     * domains of a given contact ID and requests that they update their contact
     * details.
     *
     * @param  Integer $contact->id The ID of the contact record we're updating for.
     * @return Boolean  Returns true if the update actions were successful, or false if they failed.
     */
    public function reloadContacts($contact_id)
    {
        // Currently this method is no in use, but will be added in a future update.
        // TODO: Implement automated domain updates on contact changes.
        return true;
    }

    public function getIsoCode($country)
    {
        $country_lookup = \Country::where('name', '=', $country)->first();

        if ($country_lookup && !empty($country_lookup)) {
            return $country_lookup->iso_code;
        }

        return false;
    }

    /**
     * Get extension registration fields
     *
     * Some domain extensions will require additional information when they are being
     * registered. This is especially true of some ccTLD's such as .ASIA.
     *
     * This method will attempt to retrieve the raw html custom fields needed for a
     * given domain type. Each set of fields for extensions are stored in the same
     * location in all registrar addons, so we simply look up to see if a class
     * exists, and if it does, attempt to load it and retrieve the data we need.
     *
     * @param  Domain $domain Domain data object
     * @return String         Raw html for the extra fields needed.
     */
    public function getExtensionRegistrationFields($domain)
    {
        // Determin the domain extension.
        $class = $this->loadExtensionClass($domain);

        if ($class && method_exists($class, 'getRegistrationFields')) {
            return $class->getRegistrationFields();
        }

        return false;
    }

    /**
     * Set extension registration params
     *
     * Some domain extensions will require additional information when they are being
     * registered. This is especially true of some ccTLD's such as .ASIA.
     *
     * This method will attempt to provide the custom extension handler with the
     * filled out form data provided by the getExtensionRegistrationFields method.
     *
     * It should then return the data formatted back into an array to work with
     * the given domain registrar addon. This also allows for any additional custom
     * fields relating to the tld to be added onto the registration request.
     *
     * @param  Domain $domain Domain data object
     * @return String         Raw html for the extra fields needed.
     */
    public function setExtensionRegistrationParams($domain, $custom_fields)
    {
        // Determin the domain extension.
        $class = $this->loadExtensionClass($domain);

        if ($class && method_exists($class, 'setRegistrationParams')) {
            return $class->setRegistrationParams($domain, $custom_fields);
        }

        return false;
    }

   /**
     * Get extension transfer fields
     *
     * Some domain extensions will require additional information when they are being
     * transferred. This is especially true of some ccTLD's such as .ASIA.
     *
     * This method will attempt to retrieve the raw html custom fields needed for a
     * given domain type. Each set of fields for extensions are stored in the same
     * location in all registrar addons, so we simply look up to see if a class
     * exists, and if it does, attempt to load it and retrieve the data we need.
     *
     * @param  Domain $domain Domain data object
     * @return String         Raw html for the extra fields needed.
     */
    public function getExtensionTransferFields($domain)
    {
        // Determin the domain extension.
        $class = $this->loadExtensionClass($domain);

        if ($class && method_exists($class, 'getTransferFields')) {
            return $class->getTransferFields();
        }

        return false;
    }

    /**
     * Set extension transfer params
     *
     * Some domain extensions will require additional information when they are being
     * registered. This is especially true of some ccTLD's such as .ASIA.
     *
     * This method will attempt to provide the custom extension handler with the
     * filled out form data provided by the getExtensionRegistrationFields method.
     *
     * It should then return the data formatted back into an array to work with
     * the given domain registrar addon. This also allows for any additional custom
     * fields relating to the tld to be added onto the transfer request.
     *
     * @param  Domain $domain Domain data object
     * @return String         Raw html for the extra fields needed.
     */
    public function setExtensionTransferParams($domain, $custom_fields)
    {
        // Determin the domain extension.
        $class = $this->loadExtensionClass($domain);

        if ($class && method_exists($class, 'setTransferParams')) {
            return $class->setTransferParams($domain, $custom_fields);
        }

        return false;
    }

    public function validateNameservers($domain_data)
    {
        // Manual validation on the nameservers
        $ns_valid = false;

        // If the submitted nameservers exist, and they are an array, proceed.
        if (isset($domain_data['nameservers']) && is_array($domain_data['nameservers'])) {
            $total_valid_ns = 0;

            foreach (array_filter($domain_data['nameservers']) as $key => $ns) {
                // If the nameserver isn't blank, we'll accept it for now, however
                // this will be improved in the future to do additional checks on
                // the entered value. For now however the nameserver check is handled
                // only by the registrar.
                if ($ns != '') {
                    // TODO: Implement custom nameserver validation checks.
                    $total_valid_ns++;
                }
            }

            if ($total_valid_ns >= 2) {
                // We've got two or more valid nameservers, and none that show as invalid.
                $ns_valid = true;
            }
        }

        return $ns_valid;
    }

    public function getAllExtensionContacts($domain)
    {
        // Load up related data
        $purchase = $domain->ProductPurchase()->first();
        $registrar = $domain->Registrar()->first();
        $addon = $registrar->addon()->first();
        $client = $purchase->Client()->first();

        $extension = $this->getDomainExtension($domain);

        // Work out if a contact type exists for this extension.
        $contact_extension = $extension->ContactExtension()->first();

        // Set the contact extension id to zero so that we can fall back to the
        // generic contact if we need to.
        $contact_extension_id = '0';

        if (! empty($contact_extension)) {
            // this domain extension has specific contact types needed.
            $contact_extension_id = $contact_extenson->id;
        }

        // Load all potential contacts.
        $contacts = \Contact::where('client_id', '=', $client->id)->where('contact_extension_id', '=', $contact_extension_id)->get();

        $contact_results = new \stdClass();
        $contact_results->registrant = new \StdClass();
        $contact_results->administrative = new \StdClass();
        $contact_results->technical = new \StdClass();
        $contact_results->billing = new \StdClass();

        if (!empty($contacts)) {
            foreach ($contacts as $contact) {
                if ($contact->contact_type == 'registrant') {
                    $contact_results->registrant->{$contact->id} = $contact;
                } elseif ($contact->contact_type == 'administrative') {
                    $contact_results->administrative->{$contact->id} = $contact;
                } elseif ($contact->contact_type == 'technical') {
                    $contact_results->technical->{$contact->id} = $contact;
                } elseif ($contact->contact_type == 'billing') {
                    $contact_results->billing->{$contact->id} = $contact;
                }
            }
        }

        return $contact_results;
    }

    public function formatContactsList($contacts)
    {
        $list_items = array();

        if (!empty($contacts)) {
            foreach ($contacts as $contact) {
                $list_items[$contact->id] = $contact->first_name.' '.$contact->last_name.' ('.$contact->email.') '.$contact->company;
            }
        }

        return $list_items;
    }

    public function domainYearSelection($domain)
    {
        $domain_extension = $this->getDomainExtension($domain);

        if ($domain_extension) {
            $years = array();
            for ($i=$domain_extension->min_years; $i<=$domain_extension->max_years; $i++) {
                if ($i == 1) {
                    $years[$i] = $i.' '.\App::get('translation')->get('year');
                } else {
                    $years[$i] = $i.' '.\App::get('translation')->get('years');
                }
            }

            return $years;
        }

        return array();
    }

    public function getDomainExtension($domain)
    {
        $domain_parts = $this->splitDomain($domain->domain);
        $tld = '.' . $domain_parts[1];

        return \DomainExtension::where('extension', '=', $tld)->first();
    }

    /**
     * Load Extension Class
     *
     * Searches for, and then loads a domain extension handler for a given domain.
     * These are located within an addon and allow for domain extension specific
     * fields and validation.
     *
     * @param  Domain $domain Domain data object
     * @return Class Returns either the domain extension class or false if one is not found.
     */
    public function loadExtensionClass($domain)
    {
        if (is_object($domain)) {
            $domain = $domain->domain;
        }
        $domain_parts = $this->splitDomain($domain);
        // Determin the domain extension.
        $ext = ltrim($domain_parts[1], '.');
        $extension_class = 'Domain_'.$ext;
        $extension = \DomainExtension::where('extension', '=', '.' . $ext)->first();

        $registrar = $extension->Registrar()->first();
        $addon = $registrar->Addon()->first();

        $class_name = '\Addon\\'.$addon->directory.'\Libraries\ExtensionHandlers\\'.$extension_class;

        if (class_exists($class_name)) {
            return new $class_name;
        }

        return false;
    }

    /**
     * Validate Contact
     *
     * Checkes that a provided contact ID is valid and matches the client id and
     * contact type.
     *
     * @param  Integer $client_id ID of the client who the contact should belong to
     * @param  Integer $contact_id ID of the contact record to validate against
     * @param  String $type The contact type to match againsts (e.g registrant, administrative, technical or billing)
     * @return Boolean Returns true if the contact data is valid, false if it is not
     */
    public function validateContact($client_id, $contact_id, $type)
    {
        $contact = $this->getContact($contact_id);

        if ($contact->contact_type == $type && $contact->client_id = $client_id) {
            return true;
        }

        return false;
    }

    /**
     * Set Domain Contacts
     *
     * Tells the registrar to update the domain's contact details with new ones from our own contact manager.
     *
     * @param  Domain $domain Domain data object
     * @param  Integer $registrant The registrant contact id
     * @param  Integer $administrative The administrative contact id
     * @param  Integer $technical The technical contact id
     * @param  Integer $billing The billing contact id
     * @return Object The object of return data from the registry addon.
     */
    public function setDomainContacts($domain, $registrant, $administrative, $technical, $billing)
    {

        // Load up related data
        $purchase = $domain->ProductPurchase()->first();
        $registrar = $domain->Registrar()->first();
        $addon = $registrar->addon()->first();
        $client = $purchase->Client()->first();

        $contacts = array(
            'registrant' => $registrant,
            'administrative' => $administrative,
            'technical' => $technical,
            'billing' => $billing
        );

        $request = new \stdClass();
        $request->domain = $domain;
        $request->contacts = $contacts;

        // Check the registrar addon is correctly installed, if it's not
        // there's not much point in proceeding!
        if (! \App::checkInstalledAddon($addon->directory)) {
            return false;
        }

        try {
            $return = \App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->setDomainContacts($request);

            if ($return->status == '1') {
                // Run the post-contacts hook
                \App::get('hooks')->callListeners('domain-post-contats', $domain);
            } else {
                // Run the contacts failure hook
                \App::get('hooks')->callListeners('domain-contacts-failed', $domain);
            }
        } catch (\Exception $e) {
            $return = false;
        }

        return $this->processResponse($return);
    }

    private function processResponse($response, $skip_flash_error = false)
    {
        if (is_object($response)) {
            if (isset($response->status) && $response->status == '1') {
                // The response contains the data we asked for.
                // Send the data on it's way!
                return $response;
                die();
            } elseif (isset($response->status) && $response->status == '0') {
                // The response handles things differently. Let's determin if
                // an actual error is being reported, or if we need to redirect
                // the user.
                if (isset($response->response)) {
                    // It's an error, lets set an error message before returning
                    // a false result.

                    if ($skip_flash_error) {
                        // for some methods we'll want to handle the response
                        // error manually instead of via the flash message system.
                        return $response;
                    }

                    \App::get('session')->setFlash((string)$response->response->type, \App::get('translation')->get((string)$response->response->message));
                    return true;
                    die();

                } elseif (isset($response->route)) {
                    // The addon wants to redirect to it's own page.

                    // First get the routing prefix so we know if we're a client
                    // or admin user.
                    $route_prefix = App::get('dispatcher')->getRoute()->name_prefix;

                    // Now add the prefix to the route provided by the addon.
                    $route = $route_prefix . $response->route;

                    // We can now redirect. Magic. Ok, not really.
                    return \App::redirect($route, $params);
                }
            }
        }

        // If we get to this point, something failed and the response was not
        // a success. We'll show a generic error here.
        \App::get('session')->setFlash('error', \App::get('translation')->get('an_error_occurred'));
        return false;
    }

    private function cleanDomain($domain)
    {
        // lowercase the domain
        $domain = strtolower($domain);

        // strip spaces
        $domain = str_replace(" ", "", $domain);

        // TODO: Add additional cleanup options and conversions for IDN domains.

        return $domain;
    }

    public function splitDomain($domain)
    {
        $domain = strtolower($domain);

        $domain_parts = explode(".", $domain, 2);
        return $domain_parts;
    }
}
