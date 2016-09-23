<?php

namespace App\Libraries;

class OrderHelper
{
    private $order = array();
    private $products = array();

    /**
     * Get the order status
     *
     * @param   Order Object  Object from the order model
     * @return  string        HTML string to output the orders status
     */
    public function getOrderStatus($order)
    {
        $lang = \App::get('translation');

        if($order->status == 1) {

            $str = '<span class="label label-success">' . $lang->get('active') . '</span>';

        } elseif($order->status == 2) {

            $str = '<span class="label label-danger">' . $lang->get('terminated') . '</span>';

        } else {

            $str = '<span class="label label-warning">' . $lang->get('pending') . '</span>';
        }

        return $str;
    }

    /**
     * New Order Session
     *
     * Creates a new order instance and stores it into a session. Note that the
     * order record is not created in the database at this point.
     */
    public function newOrderSession($client_id, $currency_id)
    {
        $this->order['client_id'] = $client_id;
        $this->order['currency_id'] = $currency_id;
        $this->order['promo_code'] = null;
        $this->order['products'] = null;
    }

    /**
     * Get Currency
     *
     * Returns the stored currency code id.
     */
    public function getCurrency()
    {
        if(isset($this->order['currency_id'])) {
            return $this->order['currency_id'];
        }
        return null;
    }

    /**
     * Retrieve Order Session
     *
     * Retrives the order session data and repopulates the order helper.
     */
    public function retrieveOrderSession($client_id = 0)
    {
        $order_data = \App::get('session')->getData('order_data_'.$client_id);
        if (is_null($order_data) || !isset($order_data['products'])) {
            $client = \Client::find($client_id);
            if ($client) {
                $currency_id = $client->currency_id;
            } else {
                $currency_code= \App::get('configs')->get('settings.billing.default_currency');
                $currency = \Currency::where('code', '=', $currency_code)->first();
                $currency_id = $currency->id;
            }

            $this->newOrderSession($client_id, $currency_id);
            $this->storeOrderSession();

            $order_data = \App::get('session')->getData('order_data_'.$client_id);
        }

        $this->order = $order_data['order'];
        $this->products = $order_data['products'];

        return true;
    }

    /**
     * Store Order Session
     *
     * Stores the order data into a session.
     */
    public function storeOrderSession()
    {
        \App::get('session')->setData('order_data_'.$this->order['client_id'], $this->export());
    }

    /**
     * Clear Order Session
     *
     * Clears the order session data by emptying the value.
     */
    public function clearOrderSession()
    {
        \App::get('session')->setData('order_data_'.$this->order['client_id'], null);
    }

    /**
     * Get Product Details
     *
     * Retrievs the data for a given product id, and returns the basic product
     * details, product type, and any fields that will be needed for this product.
     *
     * If we're editing a record we've already added within this order, we have the
     * option of passing an item id. This is the temporary id stored within this order
     * instance.
     */
    public function getProductDetails($product_id, $item_id = null)
    {

        $product = \Product::find($product_id);
        if (!empty($product)) {
            // Product exists.

            // If the item id exists lets load that up assuming its in the temp array
            if (isset($this->products[$item_id])) {
                $item = $this->products[$item_id];
            } else {
                $item = false;
            }

            // Get product type
            $product_type = $product->ProductType()->first();

            $pricing_data = array();
            // Get pricing options.
            if ($product_type->is_domain == '1') {

                // The product is a domain, so we get domain pricing per year.
                $domain_extension = \DomainExtension::where('product_id', '=', $product->id)->first();
                $domain_pricing = \DomainPricing::where('domain_extension_id', '=', $domain_extension->id)->where('currency_id', '=', $this->order['currency_id'])->get();

                if (!empty($domain_pricing)) {

                    foreach ($domain_pricing as $pricing) {
                        if ($pricing->years == '1') {
                            $price = $pricing->years.' '.\App::get('translation')->get('year');
                        } else {
                            $price = $pricing->years.' '.\App::get('translation')->get('years');
                        }

                        // For domains instead of just saying X period = X price
                        // we need to store the pricing for registration, renewal,
                        // transfers and restorations. When we go to put pricing
                        // together when the order and invoice gets created, we'll
                        // pull the selected_domain_action value, to work out which
                        // one of the actions we're doing for the 'selected_domain_year'.

                        $pricing_data[$pricing->years] = array(
                            'register' => $pricing->registration,
                            'renew' => $pricing->renew,
                            'transfer' => $pricing->transfer,
                            'restore' => $pricing->restore
                        );
                    }

                }
            } else {
                $product_pricing = \ProductPricing::where('product_id', '=', $product->id)->where('currency_id', '=', $this->order['currency_id'])->get();

                if (!empty($product_pricing)) {
                    foreach ($product_pricing as $pricing)
                    {
                        $pricing_data[$pricing->billing_period_id] = array(
                            'price' => $pricing->price,
                            'setup' => $pricing->setup
                        );
                    }
                }
            }

            // Pricing is now all sorted and working. The next step is to start
            // building the array of product information.

            if ($product_type->is_domain == '1') {
                $product_type = 'domain';
            } elseif ($product_type->is_hosting == '1') {
                $product_type = 'hosting';
            } else {
                $product_type = 'other';
            }

            if (!$item) {
                $product_data = array(
                    'id' => $product->id,
                    'type' => $product_type,
                    'product' => $product,
                    'pricing' => $pricing_data,
                    'fields' => array() // This is for future use.
                );
            } else {
                $product_data = $item;
                $product_data['product'] = $product;
                $product_data['pricing'] = $pricing_data;

            }

            // We now need to build up any fields for the product. As an example
            // a hosting product will have things like nameservers, and a host/domain
            // option. But a domain name will have domain and nameservers.
            //
            // In addition to this when it comes to domains we also store an action.
            // The action is to track if the action is a registration, renewal,
            // transfer or restoration.

            // As the first step, we'll store domain/hosting info we know we may
            // already have if the $item var is set.
            if ($item && $product_type == 'domain') {
                $product_data['domain_action'] = $item['domain_action'];
                $product_data['domain_years'] = $item['domain_years'];
                $product_data['domain_partial'] = $item['domain_partial'];
                $product_data['domain_extension_id'] = $item['domain_extension_id'];
            } elseif ($item && $product_type == 'hosting') {
                $product_data['billing_period'] = $item['billing_period'];
                $product_data['domain'] = $item['domain'];
                $product_data['nameservers'] = $item['nameservers'];
            }


            // Finally, we'll return the data
            return $product_data;
        }

        return false;
    }

    /**
     * Get Domain Details
     *
     * This is used on the domain order form, and is basically just pulling the
     * pricing and availability for a domain. When you fill out a domain inside
     * the box, we need to check it's available before allowing it to be ordered,
     * so you're just sending this method the domain partial, and extension.
     */
    public function getDomainDetails($partial, $extension = null)
    {
        // Just incase someone decided to be awkward and stick a 'www.' on the
        // beginning, we'll strip that now.
        if (substr($partial, 0, strlen('www.')) == 'www.') {
            $partial = substr($partial, strlen('www.'));
        }

        // Remember to check to see if the partial contains the entire domain.
        if (is_null($extension)) {
            // The extension isn't set, so we need to check to see if we can pull
            // the extension out of the domain name.
            if (strpos($partial, '.')) {
                $parts = explode('.', $partial, 1);
                $partial = $parts[0];
                $extension = '.' . $parts[1];
            } else {
                return false;
            }
        }

        if ($partial !='' && $extension !='') {
            $extension = \DomainExtension::where('extension', '=', $extension)->first();

            if (empty($extension)) {
                return false;
            }

            // The extension exists, so now we need to get the registrar and addon
            // so we can perform a registration check.
            $registrar = $extension->Registrar()->first();
            $addon = $registrar->Addon()->first();

            $availability = App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->checkDomainAvailability($partial, $extension->extension);

            return array(
                'partial' => $partial,
                'extension' => $extension->extension,
                'extension_id' => $extension->id,
                'product_id' => $extension->product_id,
                'availability' => $availability
            );
        }
    }

    /**
     * Validate Product Details
     *
     * Validates the details provided for a product. We'll also do a stock level
     * check here to ensure the item is in stock if applicable.
     *
     * @param  integer $product_id     ID of the product to validate for.
     * @param  array $product_fields Array of fields and their values to validate
     * @return Validator
     */
    public function validateProductDetails($product_id, $product_fields)
    {
        $product = \Product::find($product_id);
        if (empty($product)) {
            return false;
        }

        $product_type = $product->ProductType()->first();

        $validator = new \Whsuite\Validator\Validator();
        $validator = $validator->init();

        $validator->extend('domain', function($attribute, $value, $parameters)
        {
            return preg_match("/([a-z0-9]+\.)*[a-z0-9]+\.[a-z]+/", $value);
        });

        $validator->extend('nameserver', function($attribute, $value, $parameters)
        {
            return preg_match("/^[a-zA-Z0-9][a-zA-Z0-9.-]*[a-zA-Z0-9]$/", $value);
        });

        if ($product_type->is_domain) {
            $total_nameservers = 0;
            if (!empty($product_fields['nameservers'])) {


                foreach ($product_fields['nameservers'] as $ns) {
                    if ($total_nameservers < 2) {
                        $required = 'required|nameserver';
                    } else {
                        $required = 'nameserver';
                    }


                    $ns_validator = $validator->make(
                        array('nameserver' => $ns),
                        array('nameserver' => $required)
                    );

                    if ($ns_validator->fails()) {
                        \App\Libraries\Message::set(\App::get('translation')->formatErrors($ns_validator->messages()), 'fail');
                        return false;
                    }

                    $total_nameservers++;
                }
            }

            $validation_rules = array(
                'domain' => 'required|domain',
                'years' => 'integer|min:1|max:100',
            );

        } else {

            $validation_rules = array(
                'domain' => 'required|domain',
                'password' => 'required|min:8',
                'confirm_password' => 'required|same:password',
            );
        }

        $validation_rules['billing_period'] = 'integer|required';

        $validation = $validator->make($product_fields, $validation_rules);

        if ($validation->fails()) {
            \App\Libraries\Message::set(\App::get('translation')->formatErrors($validation->messages()), 'fail');
            return false;
        }

        return true;
    }

    /**
     * Store Product Details
     *
     * Stores the product details temporarily
     * @param  array $product_fields Array of the fields and their values to store.
     */
    public function storeProductDetails($product_fields)
    {
        if (isset($product_fields['item_id'])) {
            $this->products[$product_fields['item_id']] = $product_fields;
        } else {
            $this->products[] = $product_fields;
        }

        $this->storeOrderSession();
    }

    /**
     * Update Product Details
     *
     * Updates the stored product details
     */
    public function updateProductDetails($product_fields, $id)
    {
        $this->products[$id] = $product_fields;

        $this->storeOrderSession();
    }

    /**
     * Delete Product Details
     *
     * Deletes a stored product details
     */
    public function deleteProductDetails($id)
    {
        unset($this->products[$id]);

        $this->storeOrderSession();
    }

    /**
     * Store Product Details
     *
     * Retrieves the stored product details
     */
    public function getStoredProductDetails($item_id = null)
    {
        if (!is_null($item_id)) {
            return $this->products[$item_id];
        }

        return $this->products;
    }

    public function generateCart()
    {
        $items = array();
        $sub_total = 0;
        $taxable_total = 0;
        $total_taxed = 0;
        $total = 0;

        $tax_level_1_rate = 0;
        $tax_level_2_rate = 0;

        if (!empty($this->products)) {

            foreach ($this->products as $item_id => $item) {

                if (isset($item['product']) && is_object($item['product'])) {
                    $product = \Product::find($item['product']->id);
                } else {
                    $product = \Product::find($item['product_id']);
                }

                $product_group = $product->ProductGroup()->first();
                $product_type = $product->ProductType()->first();


                $setup_fee = 0;
                $billing_period_name = '';

                if ($product_type->is_domain == '1') {

                    $billing_period = \DomainPricing::find($item['billing_period']);

                    if ($billing_period->years == '1') {
                        $billing_period_name = $billing_period->years.' '.\App::get('translation')->get('year');
                    } else {
                        $billing_period_name = $billing_period->years.' '.\App::get('translation')->get('years');
                    }

                    if (isset($item['register'])) {
                        $action = 'register';
                        $price = $billing_period->registration;
                    } elseif (isset($item['transfer'])) {
                        $action = 'transfer';
                        $price = $billing_period->transfer;
                    } elseif (isset($item['renew'])) {
                        $action = 'renew';
                        $price = $billing_period->renewal;
                    } elseif (isset($item['restore'])) {
                        $action = 'restore';
                        $price = $billing_period->restore;
                    } else {
                        $action = 'unknown';
                        $price = 0;
                    }

                    $product_name = $item['domain'].' ('.\App::get('translation')->get($action).')';

                } elseif ($product_type->is_hosting == '1') {
                    $billing_period = \BillingPeriod::find($item['billing_period']);
                    $billing_period_name = $billing_period->name;

                    $product_name = $product->name.' ('.$item['domain'].')';

                    $pricing = \ProductPricing::where('product_id', '=', $product->id)->where('currency_id', '=', $this->getCurrency())->where('billing_period_id', '=', $item['billing_period'])->first();
                    $price = $pricing->price;

                    $setup_fee = $pricing->setup;

                } else {
                    if (isset($item['billing_period']) && $item['billing_period'] > 0) {
                        $billing_period = \BillingPeriod::find($item['billing_period']);
                        $billing_period_name = $billing_period->name;
                    } else {
                        $item['billing_period'] = 0;
                    }

                    $product_name = $product->name;

                    $pricing = \ProductPricing::where('product_id', '=', $product->id)->where('currency_id', '=', $this->getCurrency())->where('billing_period_id', '=', $item['billing_period'])->first();
                    $price = $pricing->price;

                    $setup_fee = $pricing->setup;
                }

                $addons = array();

                if (isset($item['product_addon']) && !empty($item['product_addon'])) {

                    foreach ($item['product_addon'] as $addon_id => $status) {
                        if ($status == '1') {
                            $product_addon = \ProductAddon::find($addon_id);
                            $product_addon_pricing = \ProductAddonPricing::where('addon_id', '=', $addon_id)->where('billing_period_id', '=', $item['billing_period'])->where('currency_id', '=', $this->getCurrency())->first();

                            if (!empty($product_addon) && (!empty($product_addon_pricing) || $product_addon->is_free == '1')) {

                                $addon_price = 0;

                                if ($product_addon->is_free == '0') {
                                    $addon_price = $product_addon_pricing->price;
                                }


                                $addons[] = array(
                                    'name' => $product_addon->name,
                                    'price' => $addon_price
                                );

                            }
                        }
                    }
                }

                $sub_total = $sub_total + $price + $setup_fee;

                if ($product->is_taxed == '1') {
                    $taxable_total = $taxable_total + $price + $setup_fee;
                }

                // TODO in an update - add per-item promotion code options.

                $items[$item_id] = array(
                    'name' => $product->name,
                    'period' => $billing_period_name,
                    'price' => $price,
                    'setup_fee' => $setup_fee,
                    'addons' => $addons,
                    'product_id' => $product->id
                );
            }
        }

        // Time to tax.
        if ($this->order['client_id'] != 0) {
            $client = \Client::find($this->order['client_id']);

            if ($client->count() > 0 && $client->is_taxexempt == '0') {
                $tax = \TaxLevel::getRates($client->state, $client->country);

                if (! empty($tax['level1'])) {
                    $total_taxed = $total_taxed + ($taxable_total * ($tax['level1'] / 100));

                    $tax_level_1_rate = $tax['level1'];
                }

                if (! empty($tax['level2'])) {
                    $total_taxed = $total_taxed + ($taxable_total * ($tax['level2'] / 100));

                    $tax_level_2_rate = $tax['level2'];
                }

            }
        }

        // Work out the final totals
        $total = $sub_total + $total_taxed;

        $checkout_data = array(
            'items' => $items,
            'sub_total' => $sub_total,
            'total_taxed' => $total_taxed,
            'tax_rates' => array(
                'level1' => $tax_level_1_rate,
                'level2' => $tax_level_2_rate
            ),
            'total' => $total,
            'currency_id' => $this->getCurrency()
        );

        return $checkout_data;
    }

    /**
     * Transfer Order
     *
     * If a user is logged out or not registered and has items in their order session
     * we need to transfer these to the client once they log in. This simply
     * transfers the order record over to a new session entry.
     */
    public function transferOrder($new_id)
    {
        $order_data = \App::get('session')->getData('order_data_0');
        $order_data['order']['client_id'] = $new_id;

        \App::get('session')->setData('order_data_'.$new_id, $order_data);
    }

    /**
     * Process Order
     *
     * This checks all the order and product details, and then generates an order
     * record if everything is valid, along with product records and an invoice.
     *
     * The order remains unpaid/inactive until the payment section is completed,
     * unless the specific product is set to be created on order creation (i.e
     * before we take payment).
     */
    public function processOrder()
    {
        $promotion_id = null;
        $promotion_type = 0;
        $promotion_value = null;
        if (!is_null($this->order['promo_code'])) {
            $promotion = Promotion::where('code', '=', $this->order['promo_code'])->first();
            $promotion_discount = PromotionDiscount::where('promotion_id', '=', $promotion->id)->where('currency_id', '=', $this->order['currency_id'])->first();
            $promotion_id = $promotion->id;
            $promotion_type = $promotion_discount->is_percentage;
            $promotion_value = $promotion_discount->discount;
        }

        $fraud_data = '';

        $order = new \Order();
        $order->client_id = $this->order['client_id'];
        $order->promotion_id = $promotion_id;
        $order->promotion_type = $promotion_type;
        $order->promotion_value = $promotion_value;
        $order->user_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        $order->user_hostname = gethostbyaddr(filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP));
        $order->status = 0;
        $order->fraud_output = $fraud_data;
        $order->activated_by = null;
        $order->invoice_id = null;
        $order->gateway_id = null;

        $last_order = \Order::orderBy('order_no', 'desc')->first();
        if (empty($last_order)) {
            $order->order_no = 1;
        } else {
            $last_no = ((int)$last_order->order_no)+1;
            $order->order_no = $last_no;
        }

        if ($order->save()) {
            // We've created the order record, but we are by no means done. We now
            // need to create the product purchase records, and then the invoice.
            // We need to then go back and update the order with the first invoice
            // id.
            $invoice_no = \App::get('configs')->get('settings.billing.next_invoice_number');

            \Setting::where('slug', '=', 'next_invoice_number')->increment('value');

            $invoice = new \Invoice();
            $invoice->invoice_no = $invoice_no;
            $invoice->client_id = $this->order['client_id'];
            $invoice->currency_id = $this->order['currency_id'];
            $invoice->date_due = date('Y-m-d');
            $invoice->date_paid = '0000-00-00';
            $invoice->subtotal = 0;
            $invoice->level1_rate = 0;
            $invoice->level1_total = 0;
            $invoice->level2_rate = 0;
            $invoice->level2_total = 0;
            $invoice->pre_tax_discount = 0;
            $invoice->post_tax_discount = 0;
            $invoice->total = 0;
            $invoice->total_paid = 0;
            $invoice->status = 0;

            $invoice->save();

            foreach ($this->products as $order_product) {

                $product = \Product::find($order_product['product_id']);
                $product_type = $product->ProductType()->first();

                if ($product_type->is_domain == '1') {
                    $domain_extension = \DomainExtension::where('product_id', '=', $product->id);
                    $product_pricing = \DomainPricing::find($order_product['billing_period']);

                    if (isset($order_product['register'])) {
                        $first_payment = $product_pricing->registration;
                        $recurring_payment = $product_pricing->renewal;
                        $product_name = $product->name.' ('.$order_product['domain'].') '.\App::get('translation')->get('register');
                    } elseif (isset($order_product['transfer'])) {
                        $first_payment = $product_pricing->transfer;
                        $recurring_payment = $product_pricing->renewal;
                        $product_name = $product->name.' ('.$order_product['domain'].') '.\App::get('translation')->get('transfer');
                    } elseif (isset($order_product['renew'])) {
                        $first_payment = $product_pricing->renewal;
                        $recurring_payment = $product_pricing->renewal;
                        $product_name = $product->name.' ('.$order_product['domain'].') '.\App::get('translation')->get('renew');
                    } elseif (isset($order_product['restore'])) {
                        $first_payment = $product_pricing->restore;
                        $recurring_payment = $product_pricing->renewal;
                        $product_name = $product->name.' ('.$order_product['domain'].') '.\App::get('translation')->get('restore');
                    } else {
                        // If the order product var doesnt contain something we
                        // recognise, someone's likely manipulated the order form.
                        // Just return false here - we don't want anything else to
                        // execute.
                        return false;
                    }

                    $service_type = 'domain';


                } elseif ($product_type->is_hosting == '1') {

                    if (! isset($item['billing_period'])) {
                        $item['billing_period'] = null;
                    }

                    $product_pricing = \ProductPricing::where('product_id', '=', $order_product['product_id'])->where('billing_period_id', '=', $order_product['billing_period'])->where('currency_id', '=', $this->order['currency_id'])->first();

                    $first_payment = $product_pricing->price + $product_pricing->setup;
                    $recurring_payment = $product_pricing->renewal_price;

                    $service_type = 'hosting';

                    if (isset($order_product['domain'])) {
                        $product_name = $product->name.' ('.$order_product['domain'].')';
                    } else {
                        $product_name = $product->name;
                    }
                } else {
                    if (! isset($item['billing_period'])) {
                        $item['billing_period'] = null;
                    }

                    if (! empty($item['billing_period'])) {
                        $product_pricing = \ProductPricing::where('product_id', '=', $order_product['product_id'])->where('billing_period_id', '=', $order_product['billing_period'])->where('currency_id', '=', $this->order['currency_id'])->first();
                    } else {
                        $product_pricing = \ProductPricing::where('product_id', '=', $order_product['product_id'])->where('currency_id', '=', $this->order['currency_id'])->first();
                    }

                    $first_payment = $product_pricing->price + $product_pricing->setup;
                    $recurring_payment = $product_pricing->renewal_price;

                    $product_name = $product->name;

                    $service_type = null;
                }

                $promotion_id = 0;
                if (isset($order_product['promo_code']) && $order_product['promo_code'] != '') {
                    $promotion = \Promotion::where('code', '=' ,$order_product['promo_code']);
                    $promotion_discount = \PromotionDiscount::where('promotion_id', '=', $promotion->id)->where('currency_id', '=', $this->order['currency_id'])->first();
                    if ($promotion) {
                        $promotion_id = $promotion->id;
                    }


                }

                $purchase = new \ProductPurchase();
                $purchase->client_id = $this->order['client_id'];
                $purchase->order_id = $order->id;
                $purchase->currency_id = $this->order['currency_id'];
                $purchase->product_id = $product->id;
                $purchase->billing_period_id = isset($order_product['billing_period']) ? $order_product['billing_period'] : null;
                $purchase->first_payment = $first_payment;
                $purchase->recurring_payment = $recurring_payment;
                $purchase->next_renewal = date('Y-m-d');
                $purchase->next_invoice = date('Y-m-d');
                $purchase->promotion_id = $promotion_id;
                $purchase->status = 0;

                $purchase->save();

                if ($product_type->is_domain == '1') {

                    $extension = \DomainExtension::find($order_product['extension_id']);
                    $domain_billing_period = \DomainPricing::find($order_product['billing_period']);

                    $domain = new \Domain();
                    $domain->domain = $order_product['domain'];
                    $domain->product_purchase_id = $purchase->id;
                    $domain->registrar_id = $extension->registrar_id;
                    $domain->date_registered = '0000-00-00';
                    $domain->date_expires = '0000-00-00';
                    $domain->registration_period = $domain_billing_period->years;
                    $domain->renewal_disabled = 0;
                    $domain->nameservers = rtrim(implode(", ", array_filter($order_product['nameservers'])), ", ");
                    $domain->registrar_lock = 0;
                    $domain->enable_sync = 1;
                    $domain->registrar_data = json_encode($order_product);

                    $domain->save();
                } elseif ($product_type->is_hosting == '1') {

                    $server_id = 0;
                    $servers = \Server::where('server_group_id', '=' , $product->server_group_id)->orderByRaw('priority = 0, priority ASC')->get();

                    foreach ($servers as $server) {
                        if ($server->totalAccounts() < $server->max_accounts) {
                            $server_id = $server->id;
                            break;
                        }
                    }

                    $security = \App::factory('App\Libraries\Security');

                    $hosting = new \Hosting();
                    $hosting->product_purchase_id = $purchase->id;
                    $hosting->server_id = $server_id;
                    $hosting->domain = $order_product['domain'];
                    $hosting->last_sync = 0;
                    $hosting->status = 0;
                    $hosting->username = '';
                    $hosting->password = $security->encrypt($order_product['password']);

                    if ($server_id > 0) {
                        $hosting->nameservers = $server->nameservers;
                    }
                    $hosting->save();
                }

                $purchase_item = new \InvoiceItem();
                $purchase_item->invoice_id = $invoice->id;
                $purchase_item->client_id = $this->order['client_id'];
                $purchase_item->order_id = $order->id;
                $purchase_item->product_purchase_id = $purchase->id;
                $purchase_item->service_type = $service_type;
                $purchase_item->description = $product->name;
                $purchase_item->is_taxed = $product->is_taxed;
                $purchase_item->total = $first_payment;
                $purchase_item->date_due = date('Y-m-d');

                if ($promotion_id > 0) {
                    $purchase_item->promotion_discount = $promotion_discount->discount;
                    $purchase_item->promotion_is_percentage = $promotion_discount->is_percentage;
                    $purchase_item->promotion_before_tax = $promotion->before_tax;
                }

                $purchase_item->save();

                if (isset($order_product['product_addon']) && !empty($order_product['product_addon'])) {
                    foreach ($order_product['product_addon'] as $product_addon_id => $status) {
                        if ($status == '1') {

                            $product_addon = \ProductAddon::find($product_addon_id);
                            $product_addon_pricing = \ProductAddonPricing::where('addon_id', '=', $product_addon->id)->where('currency_id', '=', $this->order['currency_id'])->first();

                            $product_addon_purchase = new \ProductAddonPurchase();
                            $product_addon_purchase->product_purchase_id = $purchase->id;
                            $product_addon_purchase->addon_id = $product_addon_id;
                            $product_addon_purchase->currency_id = $this->order['currency_id'];
                            $product_addon_purchase->first_payment = $product_addon_pricing->price;
                            $product_addon_purchase->recurring_payment = $product_addon_pricing->price;
                            $product_addon_purchase->is_active = 0;

                            $product_addon_purchase->save();

                            $addon_purchase_item = new \InvoiceItem();
                            $addon_purchase_item->invoice_id = $invoice->id;
                            $addon_purchase_item->client_id = $this->order['client_id'];
                            $addon_purchase_item->order_id = $order->id;
                            $addon_purchase_item->product_purchase_id = null;
                            $addon_purchase_item->product_addon_purchase_id = $product_addon_purchase->id;
                            $addon_purchase_item->service_type = 'addon';
                            $addon_purchase_item->description = $product_addon->name;
                            $addon_purchase_item->is_taxed = $product->is_taxed;
                            $addon_purchase_item->total = $product_addon_purchase->first_payment;
                            $addon_purchase_item->date_due = date('Y-m-d');

                            if ($promotion_id > 0) {
                                $addon_purchase_item->promotion_discount = $promotion_discount->discount;
                                $addon_purchase_item->promotion_is_percentage = $promotion_discount->is_percentage;
                                $addon_purchase_item->promotion_before_tax = $promotion->before_tax;
                            }

                            $addon_purchase_item->save();
                        }
                    }
                }

                $order->invoice_id = $invoice->id;
                $order->save();

                return $invoice->id;

            }
        }
    }

    public function activateOrder($order_id)
    {
        $order = \Order::find($order_id);

        $order->status = '1';

        $purchase_helper = \App::factory('\App\Libraries\PurchaseHelper');

        // Activate order items.
        $product_purchases = $order->ProductPurchase()->get();
        if (!empty($product_purchases)) {
            foreach ($product_purchases as $purchase) {

                if ($purchase->status != '1') {
                    $purchase_helper->processPurchase($purchase->id);
                }

            }
        }

        $order->save();

        \App::get('hooks')->callListeners('order-activated', $order);
    }

    public function terminateOrder($order_id)
    {
        $order = \Order::find($order_id);
        if ($order->status != '2') {
            $order->status = '2';
            $purchase_helper = \App::factory('\App\Libraries\PurchaseHelper');

            // Activate order items.
            $product_purchases = $order->ProductPurchase()->get();
            if (!empty($product_purchases)) {
                foreach ($product_purchases as $purchase) {
                    if($purchase->status != '2') {
                        $purchase_helper->terminatePurchase($purchase->id);
                    }
                }
            }
            $order->save();
        }
        \App::get('hooks')->callListeners('order-terminated', $order);
    }



    /**
     * Export
     *
     * Exports the order data in an array.
     * @return array array of order data
     */
    public function export()
    {
        return array('order' => $this->order, 'products' => $this->products);
    }






}
