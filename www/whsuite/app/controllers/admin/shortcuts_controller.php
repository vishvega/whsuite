<?php

use \Illuminate\Support\Str;

class ShortcutsController extends \AdminController
{
    /**
     * shortcut label for new clients
     * get the clients created in the last week
     *
     */
    public function newClients()
    {
        $Http = new \Whsuite\Http\Http;

        $Response = $Http->newResponse();
        $Response->setHeaders(
            array(
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Type' => 'text/plain'
            )
        );
        $Response->setContent(
            Client::countNew(
                \Carbon\Carbon::now()
            )
        );

        $Http->send($Response);
    }

    /**
     * shortcut label for new orders
     * get the current new (unpaid) orders
     *
     */
    public function newOrders()
    {
        $Http = new \Whsuite\Http\Http;

        $Response = $Http->newResponse();
        $Response->setHeaders(
            array(
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Type' => 'text/plain'
            )
        );
        $Response->setContent(
            Order::countNew()
        );

        $Http->send($Response);
    }

    /**
     * shortcut label for unpaid invoices
     * get the unpaid invoices
     *
     */
    public function unpaidInvoices()
    {
        $Http = new \Whsuite\Http\Http;

        $Response = $Http->newResponse();
        $Response->setHeaders(
            array(
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Type' => 'text/plain'
            )
        );
        $Response->setContent(
            Invoice::countUnpaid()
        );

        $Http->send($Response);
    }

    /**
     * shortcut label for overdue invoices
     * get the overdue invoices
     *
     */
    public function overdueInvoices()
    {
        $Http = new \Whsuite\Http\Http;

        $Response = $Http->newResponse();
        $Response->setHeaders(
            array(
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Type' => 'text/plain'
            )
        );
        $Response->setContent(
            Invoice::countOverdue()
        );

        $Http->send($Response);
    }

}
