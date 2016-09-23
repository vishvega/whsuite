<?php

/**
 * Client Order Controller
 *
 * The order controller handles both the the creation of orders.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */

use \Illuminate\Support\Str;
class OrderController extends ClientController
{
    public function listing($group_id = 0, $currency_id = 0)
    {
        if (\Whsuite\Inputs\Post::get()) {
            $data = \Whsuite\Inputs\Post::get();

            App::get('session')->setFlash('post_data', $data);

            if (isset($data['billing_period'])) {
                App::get('session')->setFlash('billing_period', $data['billing_period']);
            }
            return header("Location: ".App::get('router')->generate('client-order-new-item', array('product_id' => $data['product_id'])));
        }

        if ($group_id == 0) {
            $group = ProductGroup::where('is_visible', '=', '1')->orderBy('sort', 'ASC')->first();
        } else {
            $group = ProductGroup::find($group_id);
        }

        if ($currency_id == 0) {
            $currency = Currency::where('code', '=', App::get('configs')->get('settings.billing.default_currency'))->first();
        } else {
            $currency = Currency::find($currency_id);
        }

        $order_helper = App::factory('\App\Libraries\OrderHelper');

        if ($this->logged_in) {
            $order_helper->retrieveOrderSession($this->client->id);
        } else {
            $order_helper->retrieveOrderSession();
        }

        if (!isset($group->id)) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $products = Product::where('product_group_id', '=', $group->id)->where('is_active', '=', '1')->where('is_visible', '=', '1')->where('stock', '!=', '0')->orderBy('sort', 'asc')->get();

        $title = $this->lang->get('new_order');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $group_links = array();
        $product_groups = ProductGroup::where('is_visible', '=', '1')->get();
        foreach ($product_groups as $product_group) {
            if ($product_group->id != $group->id) {
                $group_links[App::get('router')->generate('client-order-switch', array('group_id' => $product_group->id, 'currency_id' => $currency->id))] = $product_group->name;
            }
        }

        $currency_links = array();
        $currencies = Currency::get();
        foreach ($currencies as $curr) {
            if ($curr->id != $currency_id && isset($group->id)) {
                $currency_links[App::get('router')->generate('client-order-switch', array('group_id' => $group->id, 'currency_id' => $curr->id))] = $curr->code;
            }
        }

        $billing_periods = array();
        $periods = BillingPeriod::all();
        foreach ($periods as $period) {
            $billing_periods[$period->id] = $period->name;
        }

        $is_domain = false;

        foreach ($products as $product) {
            $product_type = $product->ProductType()->first();

            if ($product_type->is_domain == '1') {
                $is_domain = true;
            }
        }

        $this->view->set('group_links', $group_links);
        $this->view->set('currency_links', $currency_links);
        $this->view->set('billing_periods', $billing_periods);
        $this->view->set('currency', $currency);
        $this->view->set('group', $group);
        $this->view->set('products', $products);
        $this->view->set('cart_items', $order_helper->getStoredProductDetails());
        $this->view->set('cart_data', $order_helper->generateCart());

        if ($is_domain == true) {
            return $this->view->display('order/domains.php');
        } else {
            return $this->view->display('order/product_group.php');
        }
    }

    public function configureItem($product_id, $item_id = null)
    {
        $order_helper = App::factory('\App\Libraries\OrderHelper');

        if ($this->logged_in) {
            $order_helper->retrieveOrderSession($this->client->id);
        } else {
            $order_helper->retrieveOrderSession();
        }

        // Determin if the product being purchased is a product type managed by
        // an addon.
        $product = Product::find($product_id);
        $product_group = $product->ProductGroup()->first();
        $product_type = $product->ProductType()->first();

        $form_data = App::get('session')->getFlash('post_data');
        if (! empty($form_data)) {
            foreach ($form_data as $key => $value) {
                if ($key == 'product_id' && $value != $product_id) {
                    break;
                }

                if (\Whsuite\Inputs\Post::get($key) == '') {
                    \Whsuite\Inputs\Post::set($key, $value);
                }
            }
        }

        // If a product type is not hosting or domain, we don't need to show any
        // configurable options. This functionality will be added in a future
        // update for custom addons to have their own configurable options.
        if ($product_type->is_hosting == 0 && $product_type->is_domain == 0) {
            $form_data = array(
                'add_to_cart' => true,
                'product_id' => $product_id
            );
        } else {
            $form_data = \Whsuite\Inputs\Post::get();
        }

        if (isset($form_data['add_to_cart'])) {
            App::get('session')->setFlash('post_data', $form_data);

            // Retrieve the stored session details for the product
            $session_form_data = App::get('session')->getFlash('post_data');
            if (is_array($session_form_data)) {
                $form_data = array_merge($form_data, $session_form_data);
            }

            // Before we store the item, we can remove the csrf field as we wont
            // want to reuse it. We'll do the same for the submit values too.
            unset($form_data['__csrf_value']);
            unset($form_data['add_to_cart']);
            unset($form_data['submit']);

            $validation_passed = false;

            if ($product_type->addon_id > 0) {
                // The product type being purchased is managed by an addon. Load
                // the addon helper so we can allow the addon to handle its own
                // validation and to allow it to do whatever it needs to do with
                // the form data.
                $addon = \Addon::find($product_type->addon_id);

                $addon_details = $addon->details();

                $addon_cameled = Str::studly($addon->directory);

                // Load the addon product types handler
                $product_type_helper = \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\ProductTypesHelper');

                $validation_result = $product_type_helper->validateAddonProductOrderData($product_type, $form_data);

                if (empty($validation_result)) {
                    $validation_passed = true;
                }

                if ($validation_passed) {
                    $form_data = $product_type_helper->updateOrderFormData($product_type, $form_data);
                }

            } else {
                // We need to now do a validation check, and if its not valid, show
                // errors. At the same time we will need to re-populate the form.
                $validation_passed = $order_helper->validateProductDetails($product_id, $form_data);
            }

            if ($validation_passed) {
                // Validation passed. We now need to add the item to the order
                // item storage, and redirect to the cart page.
                if (! is_null($item_id)) {
                    $order_helper->updateProductDetails($form_data, $item_id);
                } else {
                    $order_helper->storeProductDetails($form_data);
                }
                header("Location: ".App::get('router')->generate('client-view-cart'));
            }
        }

        if (! is_null($item_id)) {
            // get the item from the stored data
            $item_data = $order_helper->getStoredProductDetails($item_id);
            foreach ($item_data as $key => $value) {
                \Whsuite\Inputs\Post::set($key, $value);
            }

            $form_data = $item_data;
        }


        $this->view->set('product', $product);
        $this->view->set('product_group', $product_group);
        $this->view->set('product_type', $product_type);
        $this->view->set('cart_items', $order_helper->getStoredProductDetails());
        $this->view->set('cart_data', $order_helper->generateCart());

        $title = $this->lang->get('new_order');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        App::get('session')->setFlash('post_data', $form_data);

        // Load any product addons we want to offer.
        $product_addon_list = array();
        $product_addon_links = ProductAddonProduct::where('product_id', '=', $product->id)->get();
        if (! empty($product_addon_links)) {
            foreach ($product_addon_links as $product_addon_link) {
                $product_addon_pricing = ProductAddonPricing::where('addon_id', '=', $product_addon_link->product_addon_id)->where('billing_period_id', '=', $form_data['billing_period'])->where('currency_id', '=', $order_helper->getCurrency())->first();

                if (! empty($product_addon_pricing)) {
                    $product_addon = $product_addon_link->ProductAddon()->first();

                    $product_addon_list[] = array(
                        'product_addon' => $product_addon,
                        'pricing' => $product_addon_pricing
                    );

                }
            }
        }
        $this->view->set('product_addon_list', $product_addon_list);

        if ($product_type->is_hosting == '1') {
            $this->view->set('form', $this->view->fetch('order/forms/hosting.php'));
        } elseif ($product_type->is_domain == '1') {
            if (isset($form_data['billing_period']) && isset($form_data['extension_id']) && isset($form_data['domain']) && (isset($form_data['register']) || isset($form_data['transfer']) || isset($form_data['action']))) {
                if (isset($form_data['register'])) {
                    $this->view->set('action', 'register');
                } elseif (isset($form_data['transfer'])) {
                    $this->view->set('action', 'transfer');
                } elseif (isset($form_data['action'])) {
                    $this->view->set('action', $form_data['action']);
                }

                $extension = DomainExtension::find($form_data['extension_id']);

                $domain_pricing = DomainPricing::find($form_data['billing_period']);

                if (empty($domain_pricing) || $domain_pricing->domain_extension_id != $extension->id) {
                    return header('Location: '.App::get('router')->generate('client-home'));
                }

                // Pull any custom fields for the domain type, that we may need.
                $tld_ending = str_replace('.', '_', ucfirst(ltrim($extension->extension, ".")));

                if (isset($form_data['register'])) {
                    $this->view->set('registration_fields', App::get('domainhelper')->getExtensionRegistrationFields($form_data['domain']));
                } elseif (isset($form_data['transfer'])) {
                    $this->view->set('registration_fields', App::get('domainhelper')->getExtensionTransferFields($form_data['domain']));
                } else {
                    $this->view->set('registration_fields', null);
                }

                // If this is a logged in client, look for their domain contact
                // profiles and allow them to be selected if they have any.
                $registrant_contacts = array(
                    '0' => $this->lang->get('new_contact')
                );
                $administrative_contacts = array(
                    '0' => $this->lang->get('new_contact')
                );
                $technical_contacts = array(
                    '0' => $this->lang->get('new_contact')
                );
                $billing_contacts = array(
                    '0' => $this->lang->get('new_contact')
                );

                if ($this->logged_in) {
                    $contacts = Contact::where('client_id', '=', $this->client->id)->get();

                    foreach ($contacts as $contact) {
                        // If the contact is for a specific domain extension type
                        // (and not this one), skip it as it's not going to be
                        // compatible.
                        if ($contact->contact_extension_id > 0) {
                            $contact_extension = $contact->ContactExtension()->first();

                            if ($contact_extension->extension_id != $extension->id) {
                                continue;
                            }
                        }

                        if ($contact->contact_type == 'registrant') {
                            $registrant_contacts[$contact->id] = $contact->first_name.' '.$contact->last_name.' ('.$contact->email.')';
                        } elseif ($contact->contact_type == 'administrative') {
                            $administrative_contacts[$contact->id] = $contact->first_name.' '.$contact->last_name.' ('.$contact->email.')';
                        } elseif ($contact->contact_type == 'technical') {
                            $technical_contacts[$contact->id] = $contact->first_name.' '.$contact->last_name.' ('.$contact->email.')';
                        } elseif ($contact->contact_type == 'billing') {
                            $billing_contacts[$contact->id] = $contact->first_name.' '.$contact->last_name.' ('.$contact->email.')';
                        }
                    }
                }

                $contact_titles = array(
                    'Mr',
                    'Mrs',
                    'Miss',
                    'Ms'
                );

                $this->view->set('registrant_contacts', $registrant_contacts);
                $this->view->set('administrative_contacts', $administrative_contacts);
                $this->view->set('technical_contacts', $technical_contacts);
                $this->view->set('billing_contacts', $billing_contacts);
                $this->view->set('contact_titles', $contact_titles);
                $this->view->set('country_list', Country::getCountries(true));
                $this->view->set('pricing', $domain_pricing);
                $this->view->set('domain', $form_data['domain']);
                $this->view->set('form', $this->view->fetch('order/forms/domain.php'));
            } else {
                return header('Location: '.App::get('router')->generate('client-home'));
            }
        } else {
            $this->view->set('form', '');
        }

        $this->view->display('order/configureItem.php');
    }

    public function domainLookupResponse($currency_id)
    {
        if (\Whsuite\Inputs\Post::get()) {
            $data = \Whsuite\Inputs\Post::get();

            $extensions = array();
            if (isset($data['extensions'])) {
                $extensions = $data['extensions'];
            }

            if (! strpos($data['domain'], '.') && empty($extensions)) {
                return $this->view->display('order/forms/domain_lookup_failed.php');
            }

            if ($data['domain'] != '') {
                $domain_part = $data['domain'];
                $extension_list = array();

                foreach ($extensions as $id => $extension) {
                    $extension = explode("ext_", $extension);
                    $extension_list[] = $extension[1];
                }

                if (strpos($domain_part, '.') !== false) {
                    // The client/visitor has entered a full domain, instead of just
                    // the domain part. So now we have to split it all up.
                    $domain_parts = explode(".", $domain_part, 2);
                    $domain_part = $domain_parts[0];
                    $extension = '.'.$domain_parts[1];

                    $find_extension = DomainExtension::where('extension', '=', $extension)->first();
                    if (!empty($find_extension)) {
                        $extension_list[] = $find_extension->id;
                    }
                }
                $domain_extensions = DomainExtension::whereIn("id", $extension_list)->get();

                // We're now going to loop through each extension and check it's
                // availability status. To do this we need to basically individually
                // retrieve the registrar as one extension may use a different registrar
                // addon to another.
                $domains = array();
                foreach ($domain_extensions as $extension) {
                    $registrar = $extension->Registrar()->first();
                    if (empty($registrar)) {
                        continue;
                    }
                    $addon = $registrar->Addon()->first();

                    if (empty($addon)) {
                        continue;
                    }

                    $domain_name = $domain_part.$extension->extension;

                    $availability = App::get('domainhelper')->domainAvailability($domain_name);
                    $pricing = array();

                    $domain_availability = false;

                    if (isset($availability->availability)) {
                        $domain_availability = $availability->availability;
                    }

                    foreach ($extension->DomainPricing()->get() as $price) {
                        if ($price->currency_id != $currency_id) {
                            continue;
                        }

                        $domain_price = $price->registration;
                        if (isset($availability->availability) && $availability->availability == 'registered') {
                            $domain_price = $price->transfer;
                        }

                        if ($price->years == '1') {
                            $pricing[$price->id] = $price->years.' '.App::get('translation')->get('year').' ('.App::get('money')->format($domain_price, $currency_id, false, true).')';
                        } else {
                            $pricing[$price->id] = $price->years.' '.App::get('translation')->get('years').' ('.App::get('money')->format($domain_price, $currency_id, false, true).')';
                        }
                    }
                    $domains[] = array(
                        'domain' => $domain_name,
                        'extension' => $extension,
                        'availability' => $domain_availability,
                        'pricing' => $pricing,
                        'product' => $extension->Product()->first()
                    );
                }

                $this->view->set('domains', $domains);
                $this->view->display('order/domainLookupResponse.php');
            }
        }
    }

    public function deleteItem($product_id, $item_id)
    {
        $order_helper = App::factory('\App\Libraries\OrderHelper');

        if ($this->logged_in) {
            $order_helper->retrieveOrderSession($this->client->id);
        } else {
            $order_helper->retrieveOrderSession();
        }

        $order_helper->deleteProductDetails($item_id);

        return header("Location: ".App::get('router')->generate('client-view-cart'));
    }

    public function viewCart()
    {
        $order_helper = App::factory('\App\Libraries\OrderHelper');

        if ($this->logged_in) {
            $order_helper->retrieveOrderSession($this->client->id);
        } else {
            $order_helper->retrieveOrderSession();
        }

        $cart_data = $order_helper->generateCart();

        if (empty($cart_data['items'])) {
            App::get('session')->setFlash('success', $this->lang->get('cart_is_empty'));
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $post_data = \Whsuite\Inputs\Post::get();
        if (isset($post_data['checkout']) && $this->logged_in) {
            // It's now time to build the order, the items, and the invoice.
            // After that we'll redirect to the pay page for the invoice that we
            // generate. When that invoice is paid, it'll activate any products
            // if it's allowed to.

            $invoice_id = $order_helper->processOrder();

            $invoice_helper = \App::factory('\App\Libraries\InvoiceHelper');
            $invoice_helper->updateInvoice($invoice_id);

            $order_helper->clearOrderSession();

            return header("Location: ".App::get('router')->generate('client-invoice-pay', array('id' => $invoice_id)));
        }

        if ($this->logged_in) {
            $client = $this->client;
        } else {
            $client = new Client();
        }

        $this->view->set('cart', $cart_data);
        $this->view->set('country_list', Country::getCountries());
        $this->view->set('client', $client);

        $title = $this->lang->get('new_order');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->display('order/viewCart.php');
    }
}
