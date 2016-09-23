<?php

use \Whsuite\Inputs\Post as PostInput;

class AccountCreditController extends ClientController
{
    public function addCredit()
    {
        if (! $this->logged_in) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $data = PostInput::get('Invoice');
        if (! empty($data)) {
            $Currency = \Currency::find($data['currency_id']);

            $invoiceId = \Invoice::createInvoice(
                $this->client_user,
                array(
                    'level1' => 0,
                    'level2' => 0
                ),
                array(
                    'Currency' => $Currency
                )
            );

            if ($invoiceId === false) {
                \App::get('session')->setFlash('error', $this->lang->get('account_credit_invoice_failed'));
                return header("Location: ".App::get('router')->generate('client-account-credit'));
            }

            $Carbon = \Carbon\Carbon::now(
                \App::get('configs')->get('settings.localization.timezone')
            );

            $InvoiceItem = new \InvoiceItem;
            $InvoiceItem->invoice_id = $invoiceId;
            $InvoiceItem->client_id = $this->client_user->id;
            $InvoiceItem->description = $this->lang->get('account_credit');
            $InvoiceItem->is_taxed = 0;
            $InvoiceItem->total = $data['amount'];
            $InvoiceItem->date_due = $Carbon->toDateTimeString();
            $InvoiceItem->is_account_credit = 1;
            $InvoiceItem->save();

            // update and set all the totals
            $InvoiceHelper = \App::factory('\App\Libraries\InvoiceHelper');
            $InvoiceHelper->updateInvoice($invoiceId);

            \App::get('session')->setFlash('success', $this->lang->get('account_credit_invoice_created'));
            return header("Location: " . App::get('router')->generate('client-invoice-pay', array('id' => $invoiceId)));
        }

        $title = $this->lang->get('add_funds');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->display('account_credit/index.php');
    }
}
