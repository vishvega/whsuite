<?php
namespace App\Libraries\Interfaces\Hosting;

interface Shared
{
    /**
     * given the server, server group and server module in use
     * setup the server connection to use the module api / sdk
     *
     * @param object $server
     * @param object $server_group
     * @param object $server_module
     */
    public function initServer($server, $server_group, $server_module);

    /**
     * return array, currently only of hostname for manage server tab
     * to be amended soon
     *
     * @return array
     */
    public function serverDetails();

    /**
     * test the connection to the server when adding.
     * if it fails to connect, don't add it!
     *
     * @param array $serverData array of form data
     * @return bool
     */
    public function testConnection($server_data);

    /**
     * return any extra form fields needed for the product
     *
     * @return string
     */
    public function productFields();

    /**
     * create the service
     *
     * @param Object $ProductPurchase
     * @param Object $Hosting
     *
     * @return bool
     */
    public function createService($purchase, $hosting);

    /**
     * suspend the service
     *
     * @param Object $ProductPurchase
     * @param Object $Hosting
     *
     * @return bool
     */
    public function suspendService($ProductPurchase, $Hosting);

    /**
     * unsuspend the service
     *
     * @param Object $ProductPurchase
     * @param Object $Hosting
     *
     * @return bool
     */
    public function unsuspendService($ProductPurchase, $Hosting);

    /**
     * terminate the service
     *
     * @param Object $ProductPurchase
     * @param Object $Hosting
     *
     * @return bool
     */
    public function terminateService($ProductPurchase, $Hosting);
}
