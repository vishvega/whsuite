<?php
/**
 * Client Service Controller
 *
 * The service controller handles the display and management of the client's services.
 * Most of this work gets handed off to the individual addons that handle the
 * service, however we still need to be able to display basics here.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class ServiceController extends ClientController
{
    /**
     * Index
     */
    public function index($page = 1, $per_page = null)
    {
        if (!$this->logged_in) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $conditions = array(
            array(
                'type' => 'where',
                'column' => 'client_id',
                'operator' => '=',
                'value' => $this->client->id
            )
        );

        $title = $this->lang->get('services');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $purchases = ProductPurchase::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'created_at', 'desc', 'client-services-paging');
        $services = array();

        foreach($purchases as $purchase) {

            $product = $purchase->Product()->first();
            $product_type = $product->ProductType()->first();
            $hosting = null;
            $domain = null;

            if ($product_type->is_hosting == '1') {
                $type = 'hosting';
                $hosting = $purchase->Hosting()->first();

                $service_title = $product->name.' ('.$hosting->domain.')';

            } elseif ($product_type->is_domain == '1') {
                $type = 'domain';
                $domain = $purchase->Domain()->first();

                $service_title = $product->name.' ('.$domain->domain.')';

            } else {
                $type = 'other';

                $service_title = $product->name;
            }

            $services[] = (object)array(
                'purchase' => $purchase,
                'service_title' => $service_title,
                'type' => $type,
                'hosting' => $hosting,
                'domain' => $domain,
                'product' => $product
            );
        }

        $this->view->set('services', $services);
        return $this->view->display('services/index.php');
    }

    public function manageService($service_id)
    {
        $purchase = ProductPurchase::find($service_id);

        if (!$this->logged_in || $purchase->client_id != $this->client->id) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $product = $purchase->Product()->first();
        $product_type = $product->ProductType()->first();
        $product_group = $product->ProductGroup()->first();

        $hosting = false;
        $domain = false;
        $manage_route = null;

        if ($product_type->is_hosting == '1') {

            $hosting = $purchase->Hosting()->first();
            if (! is_object($hosting)) {

                \App::get('session')->setFlash('error', $this->lang->get('error_loading_service'));
                return header("Location: ".App::get('router')->generate('client-services'));
            }

            $server = $hosting->Server()->first();
            if (! is_object($server)) {

                \App::get('session')->setFlash('error', $this->lang->get('error_loading_service'));
                return header("Location: ".App::get('router')->generate('client-services'));
            }

            $server_group = $server->ServerGroup()->first();
            if (! is_object($server_group)) {

                \App::get('session')->setFlash('error', $this->lang->get('error_loading_service'));
                return header("Location: ".App::get('router')->generate('client-services'));
            }

            $server_module = $server_group->ServerModule()->first();
            if (! is_object($server_module)) {

                \App::get('session')->setFlash('error', $this->lang->get('error_loading_service'));
                return header("Location: ".App::get('router')->generate('client-services'));
            }

            $addon = $server_module->Addon()->first();
            if (! is_object($addon)) {

                \App::get('session')->setFlash('error', $this->lang->get('error_loading_service'));
                return header("Location: ".App::get('router')->generate('client-services'));
            }

            App::get('hooks')->callListeners('client-load-service-'.$addon->directory, $service_id);

            $server_ips = ServerIp::where('product_purchase_id', '=', $purchase->id)->get();
            $service_ip = '';
            if (!empty($server_ips)) {
                foreach ($server_ips as $ip) {
                    $service_ip .= $ip->ip_address.', ';
                }

                $service_ip = rtrim($service_ip, ", ");
            } else {
                $service_ip = $server->main_ip;
            }

            $this->view->set('service_ip', $service_ip);
            $this->view->set('server', $server);

        } elseif ($product_type->is_domain == '1') {

            $domain = $purchase->Domain()->first();
            if (! is_object($domain)) {

                \App::get('session')->setFlash('error', $this->lang->get('error_loading_service'));
                return header("Location: ".App::get('router')->generate('client-services'));
            }

            $registrar = $domain->Registrar()->first();
            if (! is_object($registrar)) {

                \App::get('session')->setFlash('error', $this->lang->get('error_loading_service'));
                return header("Location: ".App::get('router')->generate('client-services'));
            }

            $addon = $registrar->Addon()->first();
            if (! is_object($addon)) {

                \App::get('session')->setFlash('error', $this->lang->get('error_loading_service'));
                return header("Location: ".App::get('router')->generate('client-services'));
            }

            App::get('hooks')->callListeners('client-load-service-'.$addon->directory, $service_id);

            $manage_route = App::get('router')->generate('client-service-manage-domain', array('service_id' => $purchase->id));
        } elseif ($product_type->addon_id > 0) {
            $addon = Addon::where('id', '=', $product_type->addon_id)->first();
        }

        if (isset($addon) && $addon && (!isset($manage_route) || $manage_route == '')) {

            $manage_route = App::get('router')->generate('client-service-'.$addon->directory.'-manage', array('id' => $purchase->id));
        }

        $title = $this->lang->get('manage_service').' - '.$product->name;
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($this->lang->get('services'), 'client-services');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('purchase', $purchase);
        $this->view->set('product', $product);
        $this->view->set('product_type', $product_type);
        $this->view->set('product_group', $product_group);
        $this->view->set('hosting', $hosting);
        $this->view->set('domain', $domain);
        $this->view->set('manage_route', $manage_route);

        return $this->view->display('services/manageService.php');
    }

    public function manageDomain($service_id)
    {
        $purchase = ProductPurchase::find($service_id);

        if (!$this->logged_in || $purchase->client_id != $this->client->id) {
            return $this->lang->get('permission_denied');
        }

        $product = $purchase->Product()->first();
        $product_type = $product->ProductType()->first();
        $billing_period = $purchase->BillingPeriod()->first();
        $currency = $purchase->Currency()->first();
        $gateway = $purchase->Gateway()->first();
        $order = $purchase->Order()->first();
        $addons = $purchase->ProductAddonPurchase()->get();

        $domain = Domain::where('product_purchase_id', '=', $purchase->id)->first();

        $domain_name = $domain->domain;

        $this->view->set('domain', $domain);
        $this->view->set('purchase', $purchase);

        $domain_data = App::get('domainhelper')->getDomainInfo($domain);

        $this->view->set('domain_data', $domain_data);
        $this->view->set('date_format', 'j M Y');

        return $this->view->display('services/manageDomain.php');
    }

    public function lockDomain($service_id)
    {
        $purchase = ProductPurchase::find($service_id);

        if (!$this->logged_in || $purchase->client_id != $this->client->id) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $domain = $purchase->Domain()->first();

        if (App::get('domainhelper')->setDomainLock($domain, '0')->status == '1') {
            App::get('session')->setFlash('success', $this->lang->get('domain_updated'));
            return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('domain_update_error'));
            return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
        }
    }

    public function unlockDomain($service_id)
    {
        $purchase = ProductPurchase::find($service_id);

        if (!$this->logged_in || $purchase->client_id != $this->client->id) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $domain = $purchase->Domain()->first();

        if (App::get('domainhelper')->setDomainLock($domain, '1')->status == '1') {
            App::get('session')->setFlash('success', $this->lang->get('domain_updated'));
            return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('domain_update_error'));
            return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
        }
    }

    public function domainAuthCode($service_id)
    {
        $purchase = ProductPurchase::find($service_id);

        if (!$this->logged_in || $purchase->client_id != $this->client->id) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $product = $purchase->Product()->first();
        $domain = $purchase->Domain()->first();
        $domain_name = $domain->domain;
        $registrar = $domain->Registrar()->first();

        $auth_code = App::get('domainhelper')->getDomainAuthCode($domain);

        if (!$auth_code) {
            return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
        }

        $title = $this->lang->get('domain_authorization_code').'('.$domain->domain.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($this->lang->get('services'), 'client-services');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_service').' ('.$product->name.')',
            'client-manage-service',
            array('service_id' => $purchase->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('client', $this->client);
        $this->view->set('domain', $domain);
        $this->view->set('registrar', $registrar);
        $this->view->set('purchase', $purchase);

        $this->view->display('services/domains/auth_code.php');
    }

    public function domainNameservers($service_id)
    {
        $purchase = ProductPurchase::find($service_id);

        if (!$this->logged_in || $purchase->client_id != $this->client->id) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $product = $purchase->Product()->first();
        $domain = $purchase->Domain()->first();
        $domain_name = $domain->domain;
        $registrar = $domain->Registrar()->first();

        $nameserver_data = App::get('domainhelper')->getDomainNameservers($domain);

        if (isset($domain_data->status) && $domain_data->status != '1') {
            App::get('session')->setFlash('error', $this->lang->get('domain_not_registered'));
            return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
        }

        if (\Whsuite\Inputs\Post::get('nameservers')) {

            $post_data = \Whsuite\Inputs\Post::get();

            $nameservers = array_filter($post_data['nameservers']);

            // If the nameservers arent valid, we'll show an error.
            if (!is_array($nameservers) || count($nameservers) < 2) {
                // The validation failed.
                App::get('session')->setFlash('error', $this->lang->formatErrors('invalid_nameservers_entered'));
                return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
            } else {

                // All good - attempt to change the nameservers.
                if (App::get('domainhelper')->setDomainNameservers($domain, $nameservers)->status == '1') {
                    App::get('session')->setFlash('success', $this->lang->get('domain_updated'));
                    return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('domain_update_error'));
                    return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
                }
            }
        }

        $title = $this->lang->get('nameservers').'('.$domain->domain.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($this->lang->get('services'), 'client-services');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_service').' ('.$product->name.')',
            'client-manage-service',
            array('service_id' => $purchase->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('client', $this->client);
        $this->view->set('domain', $domain);
        $this->view->set('registrar', $registrar);
        $this->view->set('purchase', $purchase);
        $this->view->set('nameservers', $nameserver_data->nameservers);

        $this->view->display('services/domains/nameservers.php');
    }

    public function domainContacts($service_id)
    {
        $purchase = ProductPurchase::find($service_id);

        if (!$this->logged_in || $purchase->client_id != $this->client->id) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $product = $purchase->Product()->first();
        $domain = $purchase->Domain()->first();
        $domain_name = $domain->domain;
        $registrar = $domain->Registrar()->first();


        if (isset($domain_data->status) && $domain_data->status != '1') {
            App::get('session')->setFlash('error', $this->lang->get('domain_not_registered'));
            return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
        }

        if (\Whsuite\Inputs\Post::get('registrant_contact')) {

            $post_data = \Whsuite\Inputs\Post::get();

            // Check each contact is valid
            $valid_contacts = true;
            $registrant = null;
            $administrative = null;
            $technical = null;
            $billing = null;

            if (App::get('domainhelper')->validateContact($this->client->id, $post_data['registrant_contact'], 'registrant')) {
                $registrant = $post_data['registrant_contact'];
            } else {
                $valid_contacts = false;
            }

            if (App::get('domainhelper')->validateContact($this->client->id, $post_data['administrative_contact'], 'administrative')) {
                $administrative = $post_data['administrative_contact'];
            } else {
                $valid_contacts = false;
            }

            if (App::get('domainhelper')->validateContact($this->client->id, $post_data['technical_contact'], 'technical')) {
                $technical = $post_data['technical_contact'];
            } else {
                $valid_contacts = false;
            }

            if (App::get('domainhelper')->validateContact($this->client->id, $post_data['billing_contact'], 'billing')) {
                $billing = $post_data['billing_contact'];
            } else {
                $valid_contacts = false;
            }

            if ($valid_contacts && App::get('domainhelper')->setDomainContacts($domain, $registrant, $administrative, $technical, $billing)->status == '1') {
                App::get('session')->setFlash('success', $this->lang->get('domain_updated'));
                return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('domain_update_error'));
                return header("Location: ".App::get('router')->generate('client-manage-service', array('service_id' => $purchase->id)));
            }
        }

        $title = $this->lang->get('domain_contacts').'('.$domain->domain.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($this->lang->get('services'), 'client-services');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_service').' ('.$product->name.')',
            'client-manage-service',
            array('service_id' => $purchase->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('client', $this->client);
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
