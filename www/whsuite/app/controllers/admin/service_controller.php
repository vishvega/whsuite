<?php

class ServiceController extends AdminController
{
    public function listServices($page = 1, $id = null)
    {
        $title = $this->lang->get('services');

        if ($id) {
            // Temporary fix to correct paging issues.
            // TODO: Implement better fix for the page/id crossover issue.
            $client_id = $page;
            $page = $id;
            $id = $client_id;

            $client = Client::find($id);
            if (empty($client)) {
                return $this->redirect('admin-client');
            }

            // We're only listing the client's services.
            $conditions = array(
                array(
                    'type' => 'where',
                    'column' => 'client_id',
                    'operator' => '=',
                    'value' => $client->id
                )
            );

            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
            App::get('breadcrumbs')->add(
                $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
                'admin-client-profile',
                array('id' => $client->id)
            );
            App::get('breadcrumbs')->add($title);

            $this->view->set('client_credit', \App\Libraries\Transactions::allClientCredits($client->id));
            $this->view->set('client', $client);

            $services = ProductPurchase::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'created_at', 'desc', 'admin-client-services-paging', array('id' => $id, 'page' => $page));
        } else {
            // We're listing everyones invoices
            $services = ProductPurchase::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, null, 'created_at', 'desc', 'admin-services-paging', array('page' => $page));

            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($title);
        }

        App::get('breadcrumbs')->build();
        $this->view->set('title', $title);
        $this->view->set('services', $services);
        $this->view->display('services/list.php');
    }

    public function manageService($id, $service_id)
    {
        $client = Client::find($id);
        $purchase = ProductPurchase::find($service_id);
        if (empty($client) || empty($purchase)) {
            return $this->redirect('admin-client');
        }

        if ($client->id != $purchase->client_id) {
            return $this->redirect('admin-client');
        }

        $product = $purchase->Product()->first();
        $product_type = $product->ProductType()->first();
        $billing_period = $purchase->BillingPeriod()->first();
        $currency = $purchase->Currency()->first();
        $gateway = $purchase->Gateway()->first();
        $order = $purchase->Order()->first();
        $addons = $purchase->ProductAddonPurchase()->get();

        $domain_name = null; // We set this to null first as the product may not have a domain
        $type = 'other';

        $addon = false;

        if ($product_type->is_domain == '1') {
            $type = 'domain';
            $domain = Domain::where('product_purchase_id', '=', $purchase->id)->first();

            if ($domain) {
                // Set the domain-specific data for the edit details page.
                \Whsuite\Inputs\Post::set('Domain', $domain->toArray());

                // Add the registrar list to the view
                $this->view->set('registrars', Registrar::formattedList('id', 'name', array(), 'name', 'asc', true));

                $domain_name = $domain->domain;
                $registrar = $domain->Registrar()->first();

                $this->view->set('domain', $domain);
                $this->view->set('registrar', $registrar);

                $domain_data = App::get('domainhelper')->getDomainInfo($domain);

                $this->view->set('domain_data', $domain_data);
                $this->view->set('date_format', 'j M Y');
            }
        } elseif ($product_type->is_hosting == '1') {
            $type = 'hosting';
            $hosting = Hosting::where('product_purchase_id', '=', $purchase->id)->first();

            if ($hosting) {
                // Set the hosting-specific data for the edit details page.
                \Whsuite\Inputs\Post::set('Hosting', $hosting->toArray());
                // Add the server list for hosts to the view
                $this->view->set('server_list', Server::formattedListHosting(true, false));

                // Hosting accounts might have an encrypted password assigned to them, so initiate the decryption helper
                // and set it up accordingly.
                $this->view->set('decryptRoute', App::get('router')->generate('admin-hosting-decrypt-password', array('id' => $client->id, 'hosting_id' => $hosting->id)));
                $this->view->set('passphraseAuth', false);

                // For hosting accounts, if a domain has not been set, we'll leave it blank.
                if ($hosting->domain !='') {
                    $domain_name = $hosting->domain;
                }

                $this->view->set('hosting', $hosting);

                $server = null;

                if (! empty($hosting)) {
                    $server = $hosting->Server()->first();
                }
                $this->view->set('server', $server);

                $server_group = null;

                if (! empty($server)) {
                    $server_group = $server->ServerGroup()->first();
                }
                $this->view->set('server_group', $server_group);

                $server_module = null;

                if (! empty($server_group)) {
                    $server_module = $server_group->ServerModule()->first();
                }
                $this->view->set('server_module', $server_module);

                $addon = null;

                if (! empty($server_module)) {
                    $addon = $server_module->Addon()->first();

                    if (! empty($addon)) {
                        if (! \App::checkInstalledAddon($addon->directory)) {
                            $addon = false;
                        } else {
                            App::get('hooks')->callListeners('admin-load-service-'.$addon->directory, $purchase->id);
                        }
                    }
                }

                if (! empty($server)) {
                    $this->view->set('server_ips', ServerIp::ipList($server->id, 0, true));

                    if (! empty($purchase)) {
                        $this->view->set('assigned_ips', ServerIp::ipList($server->id, $purchase->id));
                    }
                }
            }
        }

        if ($purchase->promotion_id > 0) {
            $this->view->set('promotion', $purchase->Promotion()->first());
        }


        if ($addon) {
            $this->view->set('manage_route', App::get('router')->generate('admin-service-'.$addon->directory.'-manage', array('id' => $client->id, 'service_id' => $purchase->id)));
        }

        // Get a list of all addons that this service has the ability to have. This
        // will include any addons specific to this product, as well as those for
        // the product group.
        $this->view->set('addons_list', ProductAddon::addonList($product->id));

        $this->view->set('purchased_addons', $purchase->ProductAddonPurchase()->get());

        $title = $this->lang->get('manage_service').' ('.$product->name.') '.$domain_name;

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        \Whsuite\Inputs\Post::set('Client', $client->toArray());
        \Whsuite\Inputs\Post::set('Purchase', $purchase->toArray());

        $this->view->set('product', $product);
        $this->view->set('purchase', $purchase);
        $this->view->set('domain_name', $domain_name);
        $this->view->set('product_type', $product_type);
        $this->view->set('gateway', $gateway);
        $this->view->set('currency', $currency);
        $this->view->set('currencies', Currency::formattedList('id', 'code'));
        $this->view->set('billing_periods', BillingPeriod::formattedList('id', 'name', array(), 'sort', 'asc'));
        $this->view->set('promotions', Promotion::formattedList('id', 'code', array(), 'code', 'asc', true));
        $this->view->set('service_statuses', ProductPurchase::formattedStatuses());
        $this->view->set('gateways_list', Gateway::getGateways(true, true));
        $this->view->set('client', $client);
        $this->view->set('order', $order);
        $this->view->set('addons', $addons);
        $this->view->set('title', $title);
        $this->view->set('type', $type);
        $this->view->display('services/manageService.php');
    }

    public function decryptHostingPassword($hosting_id)
    {
        $crypt = \App::get('security');
        return $crypt->requestData('aes', $this->admin_user, 'Hosting', $hosting_id, 'password');
    }

    public function editServiceDetails($id, $service_id)
    {
        $client = Client::find($id);
        $purchase = ProductPurchase::find($service_id);
        if (empty($client) || empty($purchase) || !\Whsuite\Inputs\Post::get('Purchase.currency_id')) {
            return $this->redirect('admin-client');
        }

        if ($client->id != $purchase->client_id) {
            return $this->redirect('admin-client');
        }

        $product = $purchase->Product()->first();
        $product_type = $product->ProductType()->first();

        // We'll move the post data to an easier to use, shoter variable.
        $purchase_data = \Whsuite\Inputs\Post::get('Purchase');

        $validator = $this->validator->make($purchase_data, ProductPurchase::$rules);
        if ($validator->fails()) {
            // The validation failed. Find out why and send the user back to the service management page and show the error(s)
            App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));

            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        } else {
            // Before we update the service record, we want to run the domain/hosting
            // updaters, so that we can be sure that if the form contains any errors there,
            // that they are dealt with before we have updated anything.

            // Check to see if the purchase is a domain, hosting, or other 'type'.
            // If its a domain or hosting we'll be sending the data off to two
            // different (private) methods to save some additional details. This stops
            // us having to repeat a ton of code over and over.
            if ($product_type->is_hosting == '1') {
                // It's a hosting package
                if ($this->editHostingDetails($purchase_data, \Whsuite\Inputs\Post::get('Hosting'), $purchase->id) != true) {
                    // There was an error doing this bit - a flash error message
                    // would have been set, so all we need to do is redirect.
                    return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
                }
            } elseif ($product_type->is_domain == '1') {
                // It's a domain name
                if ($this->editDomainDetails($purchase_data, \Whsuite\Inputs\Post::get('Domain'), $purchase->id) !=true) {
                    // There was an error doing this bit - a flash error message
                    // would have been set, so all we need to do is redirect.
                    return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
                }
            }

            // If it wasn't a domain, and it wasn't a hosting package we just carry
            // on and continue to update the purchase details - the following
            // details get updated regardless of purchase type as its basic stuff.
            // At this point we're good to go ahead and update the purchase details,
            // as we've got the all clear that the form was filled out. And if the
            // hosting or domain details were incorrect, they'd have flagged an error
            // in their dedicated methods above.

            $purchase->currency_id = $purchase_data['currency_id'];
            $purchase->billing_period_id = $purchase_data['billing_period_id'];
            $purchase->first_payment = $purchase_data['first_payment'];
            $purchase->recurring_payment = $purchase_data['recurring_payment'];
            $purchase->next_renewal = $purchase_data['next_renewal'];
            $purchase->next_invoice = $purchase_data['next_invoice'];
            $purchase->promotion_id = $purchase_data['promotion_id'];
            $purchase->status = $purchase_data['status'];
            $purchase->disable_autosuspend = $purchase_data['disable_autosuspend'];
            $purchase->suspend_notice = $purchase_data['suspend_notice'];
            $purchase->payment_subscription = $purchase_data['payment_subscription'];
            $purchase->gateway_id = $purchase_data['gateway_id'];
            $purchase->notes = $purchase_data['notes'];

            // Time to try saving all that data
            if ($purchase->save()) {
                // Everything went great, lets set a success message and redirect.
                App::get('session')->setFlash('success', $this->lang->get('service_updated_successfully'));
                return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
            } else {
                // Something broke - lets flag it as an error and redirect.
                App::get('session')->setFlash('error', $this->lang->get('an_error_occurred'));
                return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
            }
        }
    }

    private function editHostingDetails($purchase_data, $hosting_data, $purchase_id)
    {
        // Load the purchase
        $purchase = ProductPurchase::find($purchase_id);
        $product = Product::find($purchase->product_id);
        $product_type = $product->ProductType()->first();

        // Load the hosting package
        $hosting = Hosting::where('product_purchase_id', '=', $purchase_id)->first();

        // Load the server module info
        $server = $hosting->Server()->first();
        $server_group = $server->ServerGroup()->first();
        $server_module = $server_group->ServerModule()->first();
        $addon = $server_module->Addon()->first();

        // Check the hosting data passes validation.
        $validator = $this->validator->make($hosting_data, Hosting::$rules);
        if ($validator->fails()) {
            // The validation failed. Find out why and send the user back to the service management page and show the error(s)
            App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
            return false;
        } else {
            // All clear, lets go ahead and start adding those records to the hosting var
            $hosting->server_id = $hosting_data['server_id'];
            $hosting->domain = $hosting_data['domain'];
            $hosting->nameservers = $hosting_data['nameservers'];
            $hosting->diskspace_limit = $hosting_data['diskspace_limit'];
            $hosting->bandwidth_limit = $hosting_data['bandwidth_limit'];
            $hosting->status = $purchase_data['status'];
            $hosting->username = $hosting_data['username'];

            if ($hosting_data['change_password'] !='' && $hosting_data['change_password'] === $hosting_data['change_password2']) {
                $hosting->password = App::get('security')->encrypt($hosting_data['password']);
            }

            if ($hosting->save()) {
                //Hosting details were updated, now tell the server module to
                //update the remote hosting account with any applicable changes.

                // If the server has no addon, it means its a manually managed
                // server, so dont bother trying to run an addon.
                if ($server_module->addon_id == '0') {
                    return true;
                }

                // Run the server module's updateRemote function.
                if (App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->updateRemote($purchase->id)) {
                    return true;
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('module_error'));
                    return false;
                }
            }
        }
        // If we get to here without having already been returned, we return
        // false as it means something above failed.
        return false;
    }

    private function editDomainDetails($purchase_data, $domain_data, $purchase_id)
    {
        // Load the purchase
        $purchase = ProductPurchase::find($purchase_id);
        $product = Product::find($purchase->product_id);
        $product_type = $product->ProductType()->first();

        // Load the domain
        $domain = Domain::where('product_purchase_id', '=', $purchase_id)->first();

        // Load the registrar module info
        $registrar = $domain->Registrar()->first();
        $addon = $registrar->Addon()->first();

        // Check the domain data passes validation.
        $validator = $this->validator->make($domain_data, Domain::$rules);
        if ($validator->fails()) {
            // The validation failed. Find out why and send the user back to the service management page and show the error(s)
            App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
            return false;
        } else {
            // All clear, lets go ahead and start adding those records to the domain var
            $domain->registrar_id = $domain_data['registrar_id'];

            if ($domain->save()) {
                //Hosting details were updated, now tell the server module to
                //update the remote hosting account with any applicable changes.

                // If the registrar has no addon id it means its a manually
                // managed domain registrar. So we can skip trying to run an addon.
                if ($registrar->addon_id == '0') {
                    return true;
                }

                // Run the registrar module's updateRemote function.
                if (App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->updateRemote($purchase->id)) {
                    return true;
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('module_error'));
                    return false;
                }
            }
        }
        // If we get to here without having already been returned, we return
        // false as it means something above failed.
        return false;
    }

    public function editServiceIps($id, $service_id)
    {
        $client = Client::find($id);
        $purchase = ProductPurchase::find($service_id);
        if (empty($client) || empty($purchase) || !\Whsuite\Inputs\Post::get('submit')) {
            return $this->redirect('admin-client');
        }

        if ($client->id != $purchase->client_id) {
            return $this->redirect('admin-client');
        }

        $post_data = \Whsuite\Inputs\Post::get();

        $ip_address = ServerIp::find($post_data['assign_ip']);

        if ($post_data['assign_ip'] > 0 && !empty($ip_address)) {
            $ip_address->product_purchase_id = $purchase->id;

            if (!$ip_address->save()) {
                // The ip failed to save, throw an error and redirect.
                App::get('session')->setFlash('error', $this->lang->get('error_adding_ip_address'));
                return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
            }
        }
        if (isset($post_data['assigned_ip'])) {
            foreach ($post_data['assigned_ip'] as $ip_id => $ip_state) {
                $ip_address = ServerIp::find($ip_id);

                if ($ip_state == '1') {
                    $ip_address->product_purchase_id = '0';
                    // The ip failed to save, throw an error and redirect.
                    if (!$ip_address->save()) {
                        App::get('session')->setFlash('error', $this->lang->get('error_removing_ip_address'));
                        return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
                    }
                }
            }
        }

        // Load the hosting package
        $hosting = Hosting::where('product_purchase_id', '=', $purchase->id)->first();

        // Load the server module info
        $server = $hosting->Server()->first();
        $server_group = $server->ServerGroup()->first();
        $server_module = $server_group->ServerModule()->first();
        $addon = $server_module->Addon()->first();

        // Run the server module's updateRemote function.
        if (App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->updateRemote($hosting->id)) {
            App::get('session')->setFlash('success', $this->lang->get('service_ip_addresses_updated'));
            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        } else {
            App::get('session')->setFlash('error', $this->lang->get('module_error'));
            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }
    }

    public function addAddonForm($id, $service_id)
    {
        $client = Client::find($id);
        $purchase = ProductPurchase::find($service_id);
        if (empty($client) || empty($purchase) || !\Whsuite\Inputs\Post::get('submit')) {
            return $this->redirect('admin-client');
        }

        if ($client->id != $purchase->client_id) {
            return $this->redirect('admin-client');
        }

        $product = $purchase->Product()->first();
        $currency = $purchase->Currency()->first();

        $addon_data = \Whsuite\Inputs\Post::get();

        $addon = $addon = ProductAddon::find($addon_data['addon_id']);
        if (!isset($addon_data['addon_id']) || empty($addon)) {
            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }

        // We need to get the pricing for this addon, and match it up to both the
        // currency of the product purchase, and the billing period.
        $addon_pricing = ProductAddonPricing::where('addon_id', '=', $addon->id)->where('currency_id', '=', $purchase->currency_id)->where('billing_period_id', '=', $purchase->billing_period_id)->first();

        $first_payment = 0;
        $recurring_payment = 0;

        if ($addon_pricing) {
            $billing_period = $addon_pricing->BillingPeriod()->first();
            $first_payment = $addon_pricing->price;
            if ($billing_period && $billing_period->days > 0) {
                // If the billing period has a recurring option (i.e the days
                // between bills is greater than zero), we want to set the recurring
                // payment to the price value.
                $recurring_payment = $addon_pricing->price;
            }
        }

        // Format the first/recurring payments for the current currency.
        $first_payment = App::get('money')->format($first_payment, $currency->code, true);
        $recurring_payment = App::get('money')->format($recurring_payment, $currency->code, true);

        // Add the values to the view
        $this->view->set('first_payment', $first_payment);
        $this->view->set('recurring_payment', $recurring_payment);

        $this->view->set('addon', $addon);
        $this->view->set('purchase', $purchase);
        $this->view->set('client', $client);

        $title = $this->lang->get('add_product_addon').' ('.$addon->name.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_service').' ('.$product->name.')',
            'admin-client-service',
            array('id' => $client->id, 'service_id' => $purchase->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

        $this->view->display('services/addAddon.php');
    }

    public function addAddon($id, $service_id)
    {
        $client = Client::find($id);
        $purchase = ProductPurchase::find($service_id);
        if (empty($client) || empty($purchase) || !\Whsuite\Inputs\Post::get('submit')) {
            return $this->redirect('admin-client');
        }

        if ($client->id != $purchase->client_id) {
            return $this->redirect('admin-client');
        }

        $addon_data = \Whsuite\Inputs\Post::get();

        $addon_purchase = new ProductAddonPurchase();
        $addon_purchase->product_purchase_id = $purchase->id;
        $addon_purchase->addon_id = $addon_data['addon_id'];
        $addon_purchase->currency_id = $purchase->currency_id;
        $addon_purchase->first_payment = $addon_data['first_payment'];
        $addon_purchase->recurring_payment = $addon_data['recurring_payment'];
        $addon_purchase->is_active = $addon_data['is_active'];

        $product_addon = ProductAddon::find($addon_data['addon_id']);

        $product = $purchase->Product()->first();
        $product_type = $product->ProductType()->first();

        $addon = false;

        // This is a bit long winded, but now we have to work out what (system)
        // addon (if any) is being used to actually setup and manage the hosting
        // account or domain. For hosting it's a little complex as we have to go
        // via the server, then the server group to work out which server module,
        // and its corisponding addon. This results in a fair few queries however
        // cant really be helped if we want decent forward functionality here.
        if ($product_type->is_hosting == '1') {
            $hosting = $purchase->Hosting()->first();
            $server = $hosting->Server()->first();
            $server_group = $server->ServerGroup()->first();
            $server_module = $server_group->ServerModule()->first();
            $addon = $server_module->Addon()->first();

        } elseif ($product_type->is_domain == '1') {
            $domain = $purchase->Domain()->first();
            $registrar = $domain->Registrar()->first();
            $addon = $registrar->Addon()->first();
        }

        if ($addon_purchase->save()) {
            if ($addon_data['is_active'] == '1' && $addon) {
                // The addon is being activated, and we have an addon module to use
                // so tell the server or domain module about it. This will mean
                // if the addon module wants to, it can run some of its own setup
                // commands to add the purchased addon to the client's remote
                // hosting account or domain, or whatever it may be.
                if (App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->addAddon($product_addon->id, $addon_purchase->id, $purchase->id)) {
                    App::get('session')->setFlash('success', $this->lang->get('product_addon_added_to_service'));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('module_error'));
                }
            } else {
                App::get('session')->setFlash('success', $this->lang->get('product_addon_added_to_service'));
            }
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_adding_product_addon_purchase'));
        }

        return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
    }

    public function manageAddon($id, $service_id, $addon_purchase_id)
    {
        $client = Client::find($id);
        $purchase = ProductPurchase::find($service_id);
        $addon_purchase = ProductAddonPurchase::find($addon_purchase_id);
        if (empty($client) || empty($purchase) || empty($addon_purchase)) {
            return $this->redirect('admin-client');
        }

        if ($client->id != $purchase->client_id || $addon_purchase->product_purchase_id != $purchase->id) {
            return $this->redirect('admin-client');
        }
        $addon = false;
        $product_addon = $addon_purchase->ProductAddon()->first();
        $product = $purchase->Product()->first();
        $product_type = $product->ProductType()->first();

        $addon_data = \Whsuite\Inputs\Post::get();

        if (isset($addon_data['submit'])) {
            $addon_purchase->first_payment = $addon_data['first_payment'];
            $addon_purchase->recurring_payment = $addon_data['recurring_payment'];
            $addon_purchase->is_active = $addon_data['is_active'];

            if ($product_type->is_hosting == '1') {
                $hosting = $purchase->Hosting()->first();
                $server = $hosting->Server()->first();
                $server_group = $server->ServerGroup()->first();
                $server_module = $server_group->ServerModule()->first();
                $addon = $server_module->Addon()->first();

            } elseif ($product_type->is_domain == '1') {
                $domain = $purchase->Domain()->first();
                $registrar = $domain->Registrar()->first();
                $addon = $registrar->Addon()->first();
            }

            if ($addon_purchase->save()) {
                if ($addon) {
                    // The addon is being updated, and we have an addon module to use
                    // so tell the server or domain module about it. This will mean
                    // if the addon module wants to, it can run some of its own update
                    // commands to modify the purchased addon on the client's remote
                    // hosting account or domain, or whatever it may be.
                    if (App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->updateAddon($product_addon->id, $addon_purchase->id, $purchase->id)) {
                        App::get('session')->setFlash('success', $this->lang->get('product_addon_updated'));
                    } else {
                        App::get('session')->setFlash('error', $this->lang->get('module_error'));
                    }
                } else {
                    App::get('session')->setFlash('success', $this->lang->get('product_addon_updated'));
                }
            } else {
                App::get('session')->setFlash('error', $this->lang->get('error_updating_product_addon'));
            }

            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        } else {
            $this->view->set('addon', $product_addon);
            $this->view->set('purchase', $purchase);
            $this->view->set('addon_purchase', $addon_purchase);
            $this->view->set('client', $client);

            $title = $this->lang->get('manage_product_addon').' ('.$product_addon->name.')';

            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
            App::get('breadcrumbs')->add(
                $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
                'admin-client-profile',
                array('id' => $client->id)
            );
            App::get('breadcrumbs')->add(
                $this->lang->get('manage_service').' ('.$product->name.')',
                'admin-client-service',
                array('id' => $client->id, 'service_id' => $purchase->id)
            );
            App::get('breadcrumbs')->add($title);
            App::get('breadcrumbs')->build();

            $this->view->set('title', $title);

            $this->view->display('services/manageAddon.php');
        }
    }

    public function deleteAddon($id, $service_id, $addon_purchase_id)
    {
        $client = Client::find($id);
        $purchase = ProductPurchase::find($service_id);
        $addon_purchase = ProductAddonPurchase::find($addon_purchase_id);
        if (empty($client) || empty($purchase) || empty($addon_purchae)) {
            return $this->redirect('admin-client');
        }

        if ($client->id != $purchase->client_id || $addon_purchase->product_purchase_id != $purchase->id) {
            return $this->redirect('admin-client');
        }

        $addon = false;
        $product_addon = $addon_purchase->ProductAddon()->first();
        $product = $purchase->Product()->first();
        $product_type = $product->ProductType()->first();

        if ($product_type->is_hosting == '1') {
            $hosting = $purchase->Hosting()->first();
            $server = $hosting->Server()->first();
            $server_group = $server->ServerGroup()->first();
            $server_module = $server_group->ServerModule()->first();
            $addon = $server_module->Addon()->first();

        } elseif ($product_type->is_domain == '1') {
            $domain = $purchase->Domain()->first();
            $registrar = $domain->Registrar()->first();
            $addon = $registrar->Addon()->first();
        }

        if ($addon_purchase->delete()) {
            if ($addon) {
                if (App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->deleteAddon($product_addon->id, $addon_purchase->id, $purchase->id)) {
                    App::get('session')->setFlash('success', $this->lang->get('product_addon_deleted'));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('module_error'));
                }
            } else {
                App::get('session')->setFlash('success', $this->lang->get('product_addon_deleted'));
            }
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_deleting_product_addon'));
        }

        return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
    }

    public function registerDomain($client_id, $purchase_id)
    {
        $purchase = ProductPurchase::find($purchase_id);
        $product = $purchase->Product()->first();
        $client = Client::find($client_id);
        $domain = $purchase->Domain()->first();
        $domain_name = $domain->domain;
        $registrar = $domain->Registrar()->first();

        $domain_data = App::get('domainhelper')->getDomainInfo($domain);

        if (isset($domain_data->status) && $domain_data->status == '1') {
            App::get('session')->setFlash('ok', $this->lang->get('domain_already_registered'));
            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }

        if (\Whsuite\Inputs\Post::get('years')) {
            $post_data = \Whsuite\Inputs\Post::get();

            $extension = App::get('domainhelper')->getDomainExtension($domain);

            $rules = array(
                'nameservers' => 'required',
                'years' => 'required'
            );

            $validator = $this->validator->make($post_data, $rules);

            // If the vaidator fails, or if the nameservers are valid, we'll show an error.
            if ($validator->fails() || !App::get('domainhelper')->validateNameservers($post_data)) {
                // The validation failed.
                if (count($validator->messages()) > 0) {
                    App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('registration_error_nameservers'));
                }
                return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
            } else {
                // The domain data passed validation. Time to register!

                // TODO: Currently we cant validate the custom extensions fields
                // at a later date, add a way to merge validation groups together.

                if (!isset($post_data['registrant_contact']) || !isset($post_data['administrative_contact']) ||
                    !isset($post_data['technical_contact']) || !isset($post_data['billing_contact'])) {
                    App::get('session')->setFlash('error', $this->lang->get('domain_registration_error'));
                    return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
                }

                $contacts = array(
                    'registrant' => $post_data['registrant_contact'],
                    'administrative' => $post_data['administrative_contact'],
                    'technical' => $post_data['technical_contact'],
                    'billing' => $post_data['billing_contact']
                );

                // Build the custom fields data from the post data by copying the
                // post data and then stripping out al standard fields. We'll either
                // end up with an empty array (if there are no custom fields) or an
                // array of just custom fields.
                $custom_fields = $post_data;
                unset($custom_fields['__csrf_value']);
                unset($custom_fields['years']);
                unset($custom_fields['nameservers']);
                unset($custom_fields['registrant_contact']);
                unset($custom_fields['administrative_contact']);
                unset($custom_fields['technical_contact']);
                unset($custom_fields['billing_contact']);
                unset($custom_fields['submit']);

                if (App::get('domainhelper')->registerDomain($domain, $post_data['years'], $post_data['nameservers'], $contacts, $custom_fields)) {
                    App::get('session')->setFlash('success', $this->lang->get('domain_registration_successful'));
                    return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('domain_registration_error'));
                    return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
                }
            }
        }

        $title = $this->lang->get('register_domain').'('.$domain->domain.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_service').' ('.$product->name.')',
            'admin-client-service',
            array('id' => $client->id, 'service_id' => $purchase->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('client', $client);
        $this->view->set('domain', $domain);
        $this->view->set('registrar', $registrar);
        $this->view->set('purchase', $purchase);
        $this->view->set('domain_data', $domain_data);
        $this->view->set('years', App::get('domainhelper')->domainYearSelection($domain));

        // Set contact details

        // Load up all possible contacts that are valid for this domain extension, that belong to this client.
        $applicable_contacts = \App::get('domainhelper')->getAllExtensionContacts($domain);
        $this->view->set('applicable_contacts', $applicable_contacts);

        // Generate select list data from applicable contacts
        $this->view->set('registrant_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->registrant));
        $this->view->set('administrative_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->administrative));
        $this->view->set('technical_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->technical));
        $this->view->set('billing_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->billing));

        // Retrieve any custom registration fields the extension may need.
        $this->view->set('registration_fields', App::get('domainhelper')->getExtensionRegistrationFields($domain));

        $this->view->display('services/domains/register.php');
    }

    public function renewDomain($client_id, $purchase_id)
    {
        $purchase = ProductPurchase::find($purchase_id);
        $product = $purchase->Product()->first();
        $client = Client::find($client_id);
        $domain = $purchase->Domain()->first();
        $domain_name = $domain->domain;
        $registrar = $domain->Registrar()->first();

        $domain_data = App::get('domainhelper')->getDomainInfo($domain);

        if (isset($domain_data->status) && $domain_data->status != '1') {
            App::get('session')->setFlash('error', $this->lang->get('domain_not_registered'));
            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }

        if (\Whsuite\Inputs\Post::get('years')) {
            $post_data = \Whsuite\Inputs\Post::get();

            $extension = App::get('domainhelper')->getDomainExtension($domain);

            // To validate the renewal years, we need to check on the maximum number
            // of years this particular extension can be registered for, and make
            // sure there is no attempt to go over the maximum. To do this we
            // take into account how long the domain's already registered for
            // and deduct that from the maximum registration period.

            // Extract the expiry year from the YYYY-MM-DD formatted expiry date.
            $expiry_year = substr($domain_data->date_expires, 0, 4);

            // Work out how many years are left on the current registration (if any)
            $years_left = $expiry_year - date('Y');

            // Calculate the maximum number of years you can renew for based on the
            // existing registration length.
            $max_years = $extension->max_years - $years_left;

            $min_years = $extension->min_years;

            $rules = array(
                'years' => 'required|integer|min:'.$min_years.'|max:'.$max_years
            );
            $validator = $this->validator->make($post_data, $rules);

            // If the vaidator fails, or if the nameservers are valid, we'll show an error.
            if ($validator->fails()) {
                // The validation failed.
                App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
            } else {
                // The domain data passed validation. Time to renew!

                if (App::get('domainhelper')->renewDomain($domain, $post_data['years'])->status == '1') {
                    App::get('session')->setFlash('success', $this->lang->get('domain_renewal_successful'));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('domain_renewal_error'));
                }
            }

            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }

        $title = $this->lang->get('renew_domain').'('.$domain->domain.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_service').' ('.$product->name.')',
            'admin-client-service',
            array('id' => $client->id, 'service_id' => $purchase->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('client', $client);
        $this->view->set('domain', $domain);
        $this->view->set('registrar', $registrar);
        $this->view->set('purchase', $purchase);
        $this->view->set('domain_data', $domain_data);
        $this->view->set('years', App::get('domainhelper')->domainYearSelection($domain));

        $this->view->display('services/domains/renew.php');
    }

    public function transferDomain($client_id, $purchase_id)
    {
        $purchase = ProductPurchase::find($purchase_id);
        $product = $purchase->Product()->first();
        $client = Client::find($client_id);
        $domain = $purchase->Domain()->first();
        $domain_name = $domain->domain;
        $registrar = $domain->Registrar()->first();

        if (\Whsuite\Inputs\Post::get('registrant_contact')) {
            $post_data = \Whsuite\Inputs\Post::get();

            $extension = App::get('domainhelper')->getDomainExtension($domain);

            $contacts = array(
                'registrant' => $post_data['registrant_contact'],
                'administrative' => $post_data['administrative_contact'],
                'technical' => $post_data['technical_contact'],
                'billing' => $post_data['billing_contact']
            );

            // Build the custom fields data from the post data by copying the
            // post data and then stripping out al standard fields. We'll either
            // end up with an empty array (if there are no custom fields) or an
            // array of just custom fields.
            $custom_fields = $post_data;
            unset($custom_fields['__csrf_value']);
            unset($custom_fields['registrant_contact']);
            unset($custom_fields['administrative_contact']);
            unset($custom_fields['technical_contact']);
            unset($custom_fields['billing_contact']);
            unset($custom_fields['submit']);

            if (App::get('domainhelper')->transferDomain($domain, $contacts, $post_data['auth_code'], $custom_fields)) {
                App::get('session')->setFlash('success', $this->lang->get('domain_transfer_successful'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('domain_transfer_error'));
            }
            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }

        $title = $this->lang->get('transfer_domain').'('.$domain->domain.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_service').' ('.$product->name.')',
            'admin-client-service',
            array('id' => $client->id, 'service_id' => $purchase->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('client', $client);
        $this->view->set('domain', $domain);
        $this->view->set('registrar', $registrar);
        $this->view->set('purchase', $purchase);

        // Set contact details

        // Load up all possible contacts that are valid for this domain extension, that belong to this client.
        $applicable_contacts = \App::get('domainhelper')->getAllExtensionContacts($domain);
        $this->view->set('applicable_contacts', $applicable_contacts);

        // Generate select list data from applicable contacts
        $this->view->set('registrant_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->registrant));
        $this->view->set('administrative_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->administrative));
        $this->view->set('technical_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->technical));
        $this->view->set('billing_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->billing));

        // Retrieve any custom transfer fields the extension may need.
        $this->view->set('transfer_fields', App::get('domainhelper')->getExtensionTransferFields($domain));

        $this->view->display('services/domains/transfer.php');
    }

    public function lockDomain($client_id, $purchase_id)
    {
        $purchase = ProductPurchase::find($purchase_id);
        $client = Client::find($client_id);
        $domain = $purchase->Domain()->first();

        if (App::get('domainhelper')->setDomainLock($domain, '0')->status == '1') {
            App::get('session')->setFlash('success', $this->lang->get('domain_updated'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('domain_update_error'));
        }

        return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
    }

    public function unlockDomain($client_id, $purchase_id)
    {
        $purchase = ProductPurchase::find($purchase_id);
        $client = Client::find($client_id);
        $domain = $purchase->Domain()->first();

        if (App::get('domainhelper')->setDomainLock($domain, '1')->status == '1') {
            App::get('session')->setFlash('success', $this->lang->get('domain_updated'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('domain_update_error'));
        }

        return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
    }

    public function domainAuthCode($client_id, $purchase_id)
    {
        $purchase = ProductPurchase::find($purchase_id);
        $product = $purchase->Product()->first();
        $client = Client::find($client_id);
        $domain = $purchase->Domain()->first();
        $domain_name = $domain->domain;
        $registrar = $domain->Registrar()->first();

        $auth_code = App::get('domainhelper')->getDomainAuthCode($domain);

        if (! $auth_code) {
            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }

        $auth_code = $auth_code->auth_code;

        $title = $this->lang->get('auth_code').' ('.$domain->domain.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_service').' ('.$product->name.')',
            'admin-client-service',
            array('id' => $client->id, 'service_id' => $purchase->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('client', $client);
        $this->view->set('domain', $domain);
        $this->view->set('registrar', $registrar);
        $this->view->set('purchase', $purchase);
        $this->view->set('auth_code', $auth_code);

        $this->view->display('services/domains/auth_code.php');
    }

    public function domainNameservers($client_id, $purchase_id)
    {
        $purchase = ProductPurchase::find($purchase_id);
        $product = $purchase->Product()->first();
        $client = Client::find($client_id);
        $domain = $purchase->Domain()->first();
        $domain_name = $domain->domain;
        $registrar = $domain->Registrar()->first();

        $nameserver_data = App::get('domainhelper')->getDomainNameservers($domain);

        if (isset($domain_data->status) && $domain_data->status != '1') {
            App::get('session')->setFlash('error', $this->lang->get('domain_not_registered'));
            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }

        if (\Whsuite\Inputs\Post::get('nameservers')) {
            $post_data = \Whsuite\Inputs\Post::get();

            $nameservers = array_filter($post_data['nameservers']);

            // If the nameservers arent valid, we'll show an error.
            if (!is_array($nameservers) || count($nameservers) < 2) {
                // The validation failed.
                App::get('session')->setFlash('error', $this->lang->formatErrors('invalid_nameservers_entered'));
            } else {
                // All good - attempt to change the nameservers.
                $result = App::get('domainhelper')->setDomainNameservers($domain, $nameservers);

                if ($result && $result->status == '1') {
                    App::get('session')->setFlash('success', $this->lang->get('domain_updated'));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('domain_update_error'));
                }
            }

            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }

        $title = $this->lang->get('nameservers').'('.$domain->domain.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_service').' ('.$product->name.')',
            'admin-client-service',
            array('id' => $client->id, 'service_id' => $purchase->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('client', $client);
        $this->view->set('domain', $domain);
        $this->view->set('registrar', $registrar);
        $this->view->set('purchase', $purchase);
        $this->view->set('nameservers', $nameserver_data->nameservers);

        $this->view->display('services/domains/nameservers.php');
    }

    public function domainContacts($client_id, $purchase_id)
    {
        $purchase = ProductPurchase::find($purchase_id);
        $product = $purchase->Product()->first();
        $client = Client::find($client_id);
        $domain = $purchase->Domain()->first();
        $domain_name = $domain->domain;
        $registrar = $domain->Registrar()->first();


        if (isset($domain_data->status) && $domain_data->status != '1') {
            App::get('session')->setFlash('error', $this->lang->get('domain_not_registered'));
            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }

        if (\Whsuite\Inputs\Post::get('registrant_contact')) {
            $post_data = \Whsuite\Inputs\Post::get();

            // Check each contact is valid
            $valid_contacts = true;
            $registrant = null;
            $administrative = null;
            $technical = null;
            $billing = null;

            if (App::get('domainhelper')->validateContact($client->id, $post_data['registrant_contact'], 'registrant')) {
                $registrant = $post_data['registrant_contact'];
            } else {
                $valid_contacts = false;
            }

            if (App::get('domainhelper')->validateContact($client->id, $post_data['administrative_contact'], 'administrative')) {
                $administrative = $post_data['administrative_contact'];
            } else {
                $valid_contacts = false;
            }

            if (App::get('domainhelper')->validateContact($client->id, $post_data['technical_contact'], 'technical')) {
                $technical = $post_data['technical_contact'];
            } else {
                $valid_contacts = false;
            }

            if (App::get('domainhelper')->validateContact($client->id, $post_data['billing_contact'], 'billing')) {
                $billing = $post_data['billing_contact'];
            } else {
                $valid_contacts = false;
            }

            if ($valid_contacts && App::get('domainhelper')->setDomainContacts($domain, $registrant, $administrative, $technical, $billing)->status == '1') {
                App::get('session')->setFlash('success', $this->lang->get('domain_updated'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('domain_update_error'));
            }

            return $this->redirect('admin-client-service', ['id' => $client->id, 'service_id' => $purchase->id]);
        }

        $title = $this->lang->get('domain_contacts').'('.$domain->domain.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_service').' ('.$product->name.')',
            'admin-client-service',
            array('id' => $client->id, 'service_id' => $purchase->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('client', $client);
        $this->view->set('domain', $domain);
        $this->view->set('registrar', $registrar);
        $this->view->set('purchase', $purchase);

        // Set contact details

        // Load up all possible contacts that are valid for this domain extension, that belong to this client.
        $applicable_contacts = \App::get('domainhelper')->getAllExtensionContacts($domain);
        $this->view->set('applicable_contacts', $applicable_contacts);

        // Generate select list data from applicable contacts
        $this->view->set('registrant_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->registrant));
        $this->view->set('administrative_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->administrative));
        $this->view->set('technical_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->technical));
        $this->view->set('billing_contacts', \App::get('domainhelper')->formatContactsList($applicable_contacts->billing));

        $this->view->display('services/domains/contacts.php');
    }
}
