<?php

use \Whsuite\Inputs\Post as PostInput;
use \Illuminate\Support\Str;

/**
 * Admin Automation Controller
 *
 * The automation controller is used during the nightly cron run to process things
 * like invoices, emails and other nightly tasks. It can also be used by addons
 * via the hook system.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2016, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class AutomationController extends ClientController
{
    public $cron_errors = array();

    public function runAutomation()
    {
        // To prevent cron jobs being run multiple times whislt they are still
        // processing, we set a start datetime on the record before a cron job
        // starts its tasks. Once the job finishes it clears the start time.
        // However if the job fails, we need to be able to re-run that job.
        // The fail time is used to set how long after the run starts to assume
        // that it failed so we can re-run it.

        $failTime = new DateTime();
        $failTime->sub(new DateInterval('PT1H'));
        App::get('hooks')->callListeners('automation-begin');

        // Load our list of automation periods from the database.
        $automations = Automation::whereNull('start_time')
            ->orWhere('start_time', '<=', $failTime->format('Y-m-d H:i:s'))
            ->get();

        foreach ($automations as $automation) {
            // Convert the last run time to a timestamp.
            $last_run = strtotime($automation->last_run);

            $now = time();

            // We initiate new instances of carbon at the begining and end of each
            // cron task to ensure the date used is accurate for each task.
            $Carbon = \Carbon\Carbon::now(
                \App::get('configs')->get('settings.localization.timezone')
            );

            if (! $automation->last_run || (($last_run + ($automation->run_period * 60)) <= $now)) {
                $automation->start_time = $Carbon->toDateTimeString();
                $automation->save();


                App::get('hooks')->callListeners('automation-run-'.$automation->slug);

                if (method_exists($this, 'automation_'.$automation->slug)) {
                    // A method was found in this file for the automation slug, so we'll run it.
                    $this->{'automation_'.$automation->slug}();
                }

                $Carbon = \Carbon\Carbon::now(
                    \App::get('configs')->get('settings.localization.timezone')
                );

                $automation->last_run = $Carbon->toDateTimeString();
                $automation->start_time = null;
                $automation->save();
            }
        }

        // We've also got an 'all' method that we want to call on every automation
        // run.
        $this->automation_all();

        App::get('hooks')->callListeners('automation-end', $this->cron_errors);
    }

    private function automation_30_days()
    {
    }

    private function automation_7_days()
    {
    }

    private function automation_24_hours()
    {
        // Mark expired credit/debit cards as inactive if they expire
        $this->updateClientCards();

        // Generate invoices
        $this->generateInvoices();

        // Attempt to take payment for any invoices with a vaid ACH/CC on file that are now due
        $this->attemptPayments();

        // Send first overdue invoice emails
        $this->sendFirstOverdueNotices();

        //  Send second overdue notice emails
        $this->sendSecondOverdueNotices();

        // Send third overdue notice emails
        $this->sendThirdOverdueNotices();

        // Suspend or terminate overdue accounts
        $this->suspendOrTerminateAccounts();

    }

    private function automation_1_hour()
    {
    }

    private function automation_all()
    {
        App::get('hooks')->callListeners('automation-all');
    }

    private function generateInvoices()
    {
        // Before we start, we need to update the next invoice ID just to be sure
        // we've got the right ID.
        $this->updateInvoiceCount();

        // Todays datestamp
        $today = date('Y-m-d');

        // Invoice generation offset days
        $offset = App::get('configs')->get('settings.billing.invoice_days');
        $future_date = date('Y-m-d', strtotime($today . ' + '. $offset . ' days'));

        // Load purchases that are due
        $purchases = ProductPurchase::select('product_purchases.*')
            ->join('clients', function ($join) {
                $join->on('clients.id', '=', 'product_purchases.client_id')
                    ->where('clients.status', '=', '1');
            })
            ->where('product_purchases.next_invoice', '<=', $today)
            ->where('product_purchases.status', '=', 1)
            ->get();

        if ($purchases->count() > 0) {
            foreach ($purchases as $purchase) {
                $product = $purchase->Product()->first();
                $product_type = $product->ProductType()->first();
                $client = $purchase->Client()->first();

                // First check to see if an outstanding invoice due between today and the future date exists.
                $check_invoice_items = InvoiceItem::where('product_purchase_id', '=', $purchase->id)->where('date_due', '>', $today)->get();

                if ($check_invoice_items->count() > 0) {
                    // We found an invoice item that is due after today. This means we
                    // dont need to create another invoice.

                    // We will however provide a hook right here as you never know,
                    // there may be a special circumstance where a certain addon may need
                    // to go ahead and create another invoice.
                    App::get('hooks')->callListeners('automation-generate-invoices-already-created', $purchases, $check_invoice_items);
                } else {
                    // The invoice does not exist. We need to create it!
                    App::get('hooks')->callListeners('automation-generate-invoices-pre-create', $purchases, $check_invoice_items);

                    // Work out the totals for the invoice, as well as sort out any
                    // purchased addons that need renewing too.

                    $product_subtotal = $purchase->recurring_payment;
                    $addon_subtotal = 0;
                    // Load the addons (if any)
                    $purchased_addons_link = $purchase->ProductAddonPurchase()->get();
                    if ($purchased_addons_link->count() > 0) {
                        // Addons have been purchased. We now need to go through them
                        // and work out if any need renewing. If any do need to
                        // be renewed, we'll work out their subtotals too and add
                        // it on to the invoice.
                        foreach ($purchased_addons_link as $addon_purchase) {
                            if ($addon_purchase->is_active) {
                                $addon_subtotal = $addon_subtotal + $addon_purchase->recurring_payment;
                            }
                        }
                    }

                    // Now get the invoice subtotal
                    $invoice_subtotal = $product_subtotal + $addon_subtotal;

                    // For the invoice no we're going to pull it directly from the
                    // db to ensure we're not using a cached one.
                    $invoice_number_setting = Setting::where('slug', '=', 'next_invoice_number')->first();

                    // Start creating the invoice
                    $invoice = new Invoice();
                    $invoice->invoice_no = $invoice_number_setting->value;
                    $invoice->client_id = $purchase->client_id;
                    $invoice->currency_id = $purchase->currency_id;
                    $invoice->date_due = $purchase->next_renewal;
                    $invoice->subtotal = $invoice_subtotal;

                    // Attempt to save the invoice.
                    if ($invoice->save()) {
                        // The invoice was successfully saved, so lets now carry
                        // on with everything else that needs doing before we can
                        // move onto the next invoice.

                        // Increment the next invoice number to use.
                        $invoice_number_setting->value = ($invoice_number_setting->value + 1);
                        $invoice_number_setting->save();

                        // Now we can create the invoice items for both the product,
                        // and if applicable any addons forthe product.

                        // Create the Product item
                        $product_item = new InvoiceItem();
                        $product_item->invoice_id = $invoice->id;
                        $product_item->client_id = $purchase->client_id;
                        $product_item->order_id = $purchase->order_id;
                        $product_item->product_purchase_id = $purchase->id;

                        if ($product_type->is_domain == '1') {
                            $service_type = 'domain';

                            $domain = $purchase->Domain()->first();
                            $domain_name = $domain->domain;
                            $domain_extension = $product->DomainExtension()->first();

                            // For automation we're doing a 1 year renewal only.
                            // Since some domains will start at 2 years, we'll get
                            // the first pricing record based on the lowest number
                            // of years we're renewing for.
                            $domain_pricing = DomainPricing::where('domain_extension_id', '=', $domain_extension->id)->where('currency_id', '=', $purchase->currency_id)->orderBy('years', 'asc')->first();

                            $Carbon = \Carbon\Carbon::parse(
                                $future_date,
                                \App::get('configs')->get('settings.localization.timezone')
                            );
                            $start_date = $Carbon->format($this->date['short_date']);

                            $Carbon = \Carbon\Carbon::parse(
                                $start_date,
                                \App::get('configs')->get('settings.localization.timezone')
                            );
                            $Carbon->addYears($domain_pricing->years);
                            $end_date = $Carbon->format($this->date['short_date']);

                            $product_name = $product->name;
                            $product_name .= ' - '.$domain_name;
                            $product_name .= ' (' . $domain_pricing->years . ' '.App::get('translation')->get('years').' | ' . $start_date . ' - ' . $end_date . ')';

                            $product_item->total = ($domain_pricing->years * $domain_pricing->renewal);
                        } else {
                            if ($product_type->is_hosting == '1') {
                                $service_type = 'hosting';

                                $hosting = $purchase->Hosting()->first();
                                $domain_name = $hosting->domain;
                            } else {
                                $service_type = 'other';
                                $domain_name = '';
                            }

                            $Carbon = \Carbon\Carbon::parse(
                                $future_date,
                                \App::get('configs')->get('settings.localization.timezone')
                            );
                            $start_date = $Carbon->format($this->date['short_date']);

                            $Carbon = \Carbon\Carbon::parse(
                                $start_date,
                                \App::get('configs')->get('settings.localization.timezone')
                            );

                            if ($product_type->addon_id > 0) {
                                $addon = \Addon::find($product_type->addon_id);

                                $addon_details = $addon->details();

                                $addon_cameled = Str::studly($addon->directory);

                                // Load the addon product handler
                                $product_helper = \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled);

                                // Get the next renewal date from the addon.
                                $renewal_dates = $product_helper->getNextRenewalDate($purchase->id);

                                if (isset($renewal_dates['next_renewal'])) {
                                    $next_renewal = $renewal_dates['next_renewal'];
                                }

                                if (isset($renewal_dates['next_invoice'])) {
                                    $next_invoice = $renewal_dates['next_invoice'];
                                }

                                if ($purchase->billing_period_id > 0) {
                                    $billing_period = $purchase->BillingPeriod()->first();
                                    $Carbon->addDays($billing_period->days);
                                    $end_date = $Carbon->format($this->date['short_date']);
                                }

                                $product_name = $product->name;
                                $product_name .= ' - '.$domain_name;

                                if (isset($billing_period)) {
                                    $product_name .= ' (' . $billing_period->name . ' | ' . $start_date . ' - ' . $end_date . ')';
                                }

                                $product_item->total = $purchase->recurring_payment;

                            } else {
                                $billing_period = $purchase->BillingPeriod()->first();
                                $Carbon->addDays($billing_period->days);
                                $end_date = $Carbon->format($this->date['short_date']);

                                $product_name = $product->name;
                                $product_name .= ' - '.$domain_name;
                                $product_name .= ' (' . $billing_period->name . ' | ' . $start_date . ' - ' . $end_date . ')';
                                $product_item->total = $purchase->recurring_payment;
                            }
                        }

                        $product_item->service_type = $service_type;

                        // If the service has a domain or hostname, we'll add it to the item name.
                        $product_item->description = $product_name;

                        $product_item->is_taxed = $product->is_taxed;
                        $product_item->date_due = $future_date;

                        // Work out if a discount needs applying
                        $discount_total = 0;
                        $discount_is_percentage = 0;
                        $discount_before_tax = 0;
                        if ($purchase->promotion_id > 0) {
                            $promotion = $purchase->Promotion()->first();

                            $promo = PromotionDiscount::where('currency_id', '=', $purchase->currency_id)->where('promotion_id', '=', $purchase->promotion_id)->first();
                            if ($promo->count() > 0) {
                                $discount_total = $promo->discount;
                            }

                            $discount_total = $promotion->discount;
                            $discount_is_percentage = $promotion->is_percentage;
                            $discount_before_tax = $promotion->before_tax;
                        }

                        $product_item->promotion_discount = $discount_total;
                        $product_item->promotion_is_percentage = $discount_is_percentage;
                        $product_item->promotion_before_tax = $discount_before_tax;

                        $product_item->save();

                        // Now add any items for product addons. For now these are
                        // pretty simple, however we'll add extra features such
                        // as per-addon discounts at a later date.
                        foreach ($purchased_addons_link as $addon_purchase) {
                            $product_addon = $addon_purchase->ProductAddon()->first();

                            $addon_item = new InvoiceItem();
                            $addon_item->invoice_id = $invoice->id;
                            $addon_item->client_id = $purchase->client_id;
                            $addon_item->order_id = $purchase->order_id;
                            $addon_item->product_addon_purchase_id = $addon_purchase->id;
                            $addon_item->service_type = 'addon';
                            $addon_item->description = $product_addon->name;
                            $addon_item->is_taxed = $product->is_taxed;
                            $addon_item->total = $addon_purchase->recurring_payment;
                            $addon_item->date_due = $future_date;
                            $addon_item->save();
                        }

                        // We've added all the items, and we've got a basic invoice
                        // subtotal. However we need a firm total based on tax,
                        // discounts, etc. So lets now work that out and update
                        // the invoice.
                        App::factory('\App\Libraries\InvoiceHelper')->updateInvoice($invoice->id);

                        // Now we need to email the invoice creation notice to the
                        // client.
                        App::factory('\App\Libraries\InvoiceHelper')->emailInvoice($invoice->id);

                    } else {
                        // The invoice failed. Because this is cron, it wouldn't make
                        // any sense at all to stop everything at this point, so
                        // instead we log the errors, and send them out in the cron
                        // email at the end.

                        $error = 'Invoice create for purchase id ' . $purchase->id .' (client id ' . $purchase->client_id .') failed and skipped.';
                        $this->cron_errors[] = $error;
                    }
                }
                App::get('hooks')->callListeners('automation-generate-invoices-post-create', $purchase, $check_invoice_items);
            }
        }
    }

    private function attemptPayments()
    {
        // Load all invoices with an unpaid balance
        $invoices = \Invoice::whereRaw('total_paid <= total')->where('status', '=', '0')->get(); // Annoyingly we have to use whereRaw here due to Eloquent's odd handling of field names as conditions.

        // Hook for any actions before we start doing stuff to invoices.
        App::get('hooks')->callListeners('automation-attempt-payment-pre-loop', $invoices);

        if ($invoices->count() < 1) {
            // No invoices to attempt payment on - lets leave.
            return true;
        } else {
            // Found invoices that need paying.

            foreach ($invoices as $invoice) {
                // Before we continue, we check to see if the client that owns this
                // invoice has a credit card or ach account on file. If they dont,
                // we cant really attempt a payment. We do however provide a hook
                // here so that if someone wants to create a different gateway type,
                // it can run its own checks or actions on the invoice, and attempt
                // a different type of payment.
                App::get('hooks')->callListeners('automation-attempt-payment-pre-attempt', $invoice);

                App::factory('\App\Libraries\InvoiceHelper')->attemptPayment($invoice->id);

                App::get('hooks')->callListeners('automation-attempt-payment-post-attempt', $invoice);
            }
        }

        // Hook for any actions after we've looped through the invoices.
        App::get('hooks')->callListeners('automation-attempt-payment-post-loop', $invoices);

        // If we get to this point, we're all done. We've not got any errors to
        // log if a payment fails, so we return true here.
        return true;
    }

    private function sendFirstOverdueNotices()
    {
        $overdue_days = \App::get('configs')->get('settings.billing.first_overdue_notice_days');
        $overdue_days = intval($overdue_days);

        if ($overdue_days === 0) {
            return;
        }

        $Carbon = \Carbon\Carbon::now(
            \App::get('configs')->get('settings.localization.timezone')
        );
        $Carbon->subDays($overdue_days);
        $date = $Carbon->toDateString();

        return $this->sendOverdueNotices($date, 'invoice_overdue_first_notice');
    }

    private function sendSecondOverdueNotices()
    {
        $overdue_days = \App::get('configs')->get('settings.billing.second_overdue_notice_days');
        $overdue_days = intval($overdue_days);

        if ($overdue_days === 0) {
            return;
        }

        $Carbon = \Carbon\Carbon::now(
            \App::get('configs')->get('settings.localization.timezone')
        );
        $Carbon->subDays($overdue_days);
        $date = $Carbon->toDateString();

        return $this->sendOverdueNotices($date, 'invoice_overdue_second_notice');
    }

    private function sendThirdOverdueNotices()
    {
        $overdue_days = \App::get('configs')->get('settings.billing.third_overdue_notice_days');
        $overdue_days = intval($overdue_days);

        if ($overdue_days === 0) {
            return;
        }

        $Carbon = \Carbon\Carbon::now(
            \App::get('configs')->get('settings.localization.timezone')
        );
        $Carbon->subDays($overdue_days);
        $date = $Carbon->toDateString();

        return $this->sendOverdueNotices($date, 'invoice_overdue_third_notice');
    }

    private function sendOverdueNotices($date, $template_slug)
    {
        $invoices = \Invoice::where('date_due', '=', $date)->where('status', '=', '0')->get();

        foreach ($invoices as $invoice) {
            $items = $invoice->InvoiceItem()->get();

            foreach ($items as $item) {
                if ($item->product_purchase_id > 0) {
                    $purchase = $item->ProductPurchase()->first();

                    if ($purchase->status == $purchase::PENDING || $purchase->status == $purchase::TERMINATED) {
                        continue 2;
                    }
                }
            }

            $client = $invoice->Client()->first();
            $currency = $invoice->Currency()->first();

            $Carbon = \Carbon\Carbon::parse(
                $invoice->date_due,
                \App::get('configs')->get('settings.localization.timezone')
            );
            $date_due = $Carbon->format($this->date['short_date']);

            $data = array(
                'invoice' => $invoice,
                'items' => $items,
                'client' => $client,
                'currency' => $currency,
                'date_due' => $date_due,
                'total_due' => \App::get('money')->format(($invoice->total - $invoice->total_paid), $currency->code).' '.$currency->code,
            );
            \App::get('email')->sendTemplateToClient($invoice->client_id, $template_slug, $data);
        }
    }

    private function suspendOrTerminateAccounts()
    {
        $today = date('Y-m-d');
        // Load all purchases that are overdue and are active
        $purchases = ProductPurchase::where('next_renewal', '<', $today)->where('status', '=', '1')->get();
        foreach ($purchases as $purchase) {
            $product = $purchase->Product()->first();
            $product_type = $product->ProductType()->first();
            $purchased_addons = $purchase->ProductAddonPurchase()->get();
            $addon = null;

            if ($product_type->is_hosting) {
                $hosting = $purchase->Hosting()->first();
                $server = $hosting->Server()->first();
                $server_group = $server->ServerGroup()->first();
                $server_module = $server_group->ServerModule()->first();
                $addon = $server_module->Addon()->first();
            } elseif ($product_type->is_doman) {
                $domain = $product->Domain()->first();
                $registrar = $domain->Registrar()->first();
                $addon = $registrar->Addon()->first();
            } else {
                $addon = \Addon::find($product_type->addon_id);
            }
            // Work out if we need to suspend or terminate the account.

            $terminate_timestamp = strtotime($purchase->next_renewal) + ($product->auto_terminate_days * 86400);
            $suspend_timestamp = strtotime($purchase->next_renewal) + ($product->auto_suspend_days * 86400);
            $addon_cameled = Str::studly($addon->directory);

            if ($product->auto_terminate_days > 0 && $terminate_timestamp < time()) {
                // The product is eligable for termination.
                if ($addon) {
                    if ($product_type->is_domain == '1') {
                        \App::factory('\\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled)
                            ->terminateService($domain->id);
                    } elseif ($product_type->is_hosting == '1') {
                        \App::factory('\\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled)
                            ->terminateService($purchase, $hosting);
                    } else {
                        \App::factory('\\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled)
                            ->terminateService($purchase->id);
                    }
                }

                // Mark as terminated.
                $purchase->status = $purchase::TERMINATED;
                $purchase->save();

                foreach ($purchased_addons as $purchased_addon) {
                    $purchased_addon->is_active = '0';
                    $purchased_addon->save();

                    App::get('hooks')->callListeners('automation-purchase-terminated-addon-deactivated', $purchased_addon);
                }

            } elseif ($product->auto_suspend_days > 0 && $suspend_timestamp < time() && $purchase->disable_autosuspend < 1) {
                // The product is eligable for suspension.
                if ($addon) {
                    if ($product_type->is_domain == '1') {
                        \App::factory('\\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled)
                            ->suspendService($domain->id);
                    } elseif ($product_type->is_hosting == '1') {
                        \App::factory('\\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled)
                            ->suspendService($hosting->id);
                    } else {
                        \App::factory('\\Addon\\' . $addon_cameled . '\\Libraries\\' . $addon_cameled)
                            ->suspendService($purchase->id);
                    }
                }

                // Mark as suspended.
                $purchase->status = $purchase::SUSPENDED;
                $purchase->save();

                $data = array(
                    'purchase' => $purchase,
                    'product' => $product,
                    'client' => $purchase->Client()->first(),
                );
                \App::get('email')->sendTemplateToClient($purchase->client_id, 'service_suspended', $data);
            }
        }
    }

    private function updateClientCards()
    {
        $cards = ClientCc::where('is_active', '=', '1')->get();

        foreach ($cards as $cc) {
            // Decrypt the expiry date and last 4 digits
            $cc_account_last4 = \App::get('security')->decrypt($cc->account_last4);
            $cc_account_expiry = \App::get('security')->decrypt($cc->account_expiry);

            if (strlen($cc_account_expiry) != 4 || !is_numeric($cc_account_expiry)) {
                continue;
            }

            // Split the month and year
            $expiry_month = substr($cc_account_expiry, 0, 2);
            $expiry_year = substr($cc_account_expiry, -2, 2);

            // Put that into a standard date format with it set to
            // the 1st of the month
            $expiry_date = $expiry_year . '-' . $expiry_month . '-' . '01';

            // Work out the expiry timestamp based on the above. This
            // basically creates a timestamp, set to 1 second past
            // the expiry date. So if a card expires at 23:59:59 on
            // December 31 2020 this value will be for 12:00:00 am
            // on January 1, 2021.
            $expiry_timestamp = strtotime("+1 month", strtotime($expiry_date));
            if ($expiry_timestamp < time()) {
                // The card has expired and cant be used.
                $cc->is_active = '0';
                $cc->save();

                App::get('hooks')->callListeners('automation-client-card-deactivated', $cc);

                $data = array(
                    'last_4' => $cc_account_last4,
                    'client' => $cc->Client()->first(),
                );
                \App::get('email')->sendTemplateToClient($cc->client_id, 'card_expired', $data);
            }
        }
    }

    private function updateInvoiceCount()
    {
        $invoice_count = \App::get('configs')->get('settings.billing.next_invoice_number');

        $last_invoice = \Invoice::orderBy('invoice_no', 'desc')->first();
        if (! empty($last_invoice)) {
            if ($last_invoice->invoice_no >= $invoice_count) {
                // The invoice number stored is too low, so lets increase it.
                $invoice_no = \Setting::where('slug', '=', 'next_invoice_number')->first();
                $invoice_no->value = $last_invoice->invoice_no + 1;
                $invoice_no->save();

            }
        }
    }
}
