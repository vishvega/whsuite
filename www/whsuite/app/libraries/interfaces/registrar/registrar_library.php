<?php
namespace App\Libraries\Interfaces\Registrar;

interface RegistrarLibrary
{

    /**
     * Get Domain Info
     *
     * Fetches the basic domain data from the registry to provide an overview
     * of the domain, it's registration period, lock status, etc.
     *
     * @param  Object $request An object containing the domain data.
     * @return Object Returns a result object containing either error responses or the requested data.
     */
    public function getDomainInfo($request);

    /**
     * Register Domain
     *
     * Tells the registry to register the domain name for a given period of time
     * and sets the contacts, nameservers, etc.
     *
     * @param  Object $request An object containing the domain data.
     * @return Object Returns a result object containing either error responses or the requested data.
     */
    public function registerDomain($request);

    /**
     * Renew Domain
     *
     * Renews the domain name for a given period of time.
     *
     * @param  Object $request An object containing the domain data.
     * @return Object Returns a result object containing either error responses or the requested data.
     */
    public function renewDomain($request);

    /**
     * Transfer Domain
     *
     * Tells the registry to request a transfer of the domain name and sets the
     * contacts.
     *
     * @param  Object $request An object containing the domain data.
     * @return Object Returns a result object containing either error responses or the requested data.
     */
    public function transferDomain($request);

    /**
     * Set Domain Lock
     *
     * Sets the domain to either locked or unlocked depending on the request.
     *
     * @param  Object $request An object containing the domain data.
     * @return Object Returns a result object containing either error responses or the requested data.
     */
    public function setDomainLock($request);

    /**
     * Get Domain Auth Code
     *
     * Tells Enom to email the auth code to the domain owner, as they do not
     * currently support getting the auth code directly. Because of this we'll
     * return a message based response to the domain helper.
     *
     * @param  Object $request An object containing the domain data.
     * @return Object Returns a result object containing either error responses or the requested data.
     */
    public function getDomainAuthCode($request);

    /**
     * Get Domain Nameservers
     *
     * Retrieves the current nameservers for the domain.
     *
     * @param  Object $request An object containing the domain data.
     * @return Object Returns a result object containing either error responses or the requested data.
     */
    public function getDomainNameservers($request);

    /**
     * Set Domain Nameservers
     *
     * Sets the nameservers for the domain.
     *
     * @param  Object $request An object containing the domain data.
     * @return Object Returns a result object containing either error responses or the requested data.
     */
    public function setDomainNameservers($request);

    /**
     * Set Domain Contacts
     *
     * Tells the registry to update the contact details for the domain whois.
     *
     * @param  Object $request An object containing the domain data.
     * @return Object Returns a result object containing either error responses or the requested data.
     */
    public function setDomainContacts($request);

    /**
     * Update Remote
     *
     * The update remote method is primarily used for hosting, and allows WHSuite
     * to request that a hosting account is updated. With hosting accounts the
     * param parsed is the hosting account id. However for domains and other services
     * we parse the purchase id.
     *
     * In most cases domains have no need to perform a remote update as you cant
     * exactly go and tell a domain registrar to modify the expiry date, or anything like
     * that. So for now, we dont actually do anything at all, and just return true.
     *
     * We've left this method here purely for any future developments or special
     * cases that do require you to update a registrar. The method does still need
     * to be included even if not in use, and should simply return true.
     *
     * @param  int $id The id of the client who owns the service
     * @param  int $service_id The id of the purchased service (aka purchase id)
     * @return boolean Return true to skip remote updates.
     */
    public function updateRemote($purchase_id);

    /**
     * Product Fields
     *
     * Returns form fields specific to domains registered through this registrar
     * on the product management page.
     *
     * @param  int $extension_id The id of the domain extension
     * @param  int $service_id The id of the purchased service (aka purchase id)
     * @return string Returns the HTML form that gets injected into the product management page.
     */
    public function productFields($extension_id);

    /**
     * Terminate Service
     *
     * With domains many registrar addons wont want to, nor provide a full option
     * to terminate a domain, and will instead either disable this functionality
     * or will use a form of locking on the domain to prevent modifications.
     *
     * We'll re-review domain terminations in the future to see if there's a
     * more complete and consistent way of terminating domains.
     *
     * @param  int $domain_id The id of the domain to terminate
     * @return bool Returns the status of the termination
     */
    public function terminateService($domain_id);

    /**
     * Suspend Service
     *
     * Suspends the domain with a generic suspention notice. Note: domain
     * suspensions are not supported by some registrar addons, and this method
     * may not perform any actions on some domains. If the registry API offers
     * a suspend service, use it here.
     *
     * @param  int $domain_id The id of the domain name
     * @return string Returns true if the action was successful.
     */
    public function suspendService($domain_id);

    /**
     * Unsuspend Service
     *
     * Unsuspends the domain with a generic suspention notice. Note: domain
     * suspensions are not supported by some registrar addons, and this method
     * may not perform any actions on some domains. If the registry API offers
     * an unsuspend service, use it here.
     *
     * @param  int $domain_id The id of the domain name
     * @return string Returns true if the action was successful.
     */
    public function unsuspendService($domain_id);

    /**
     * Check Availability
     *
     * Checks the availability of a domain name.
     *
     * @param  Object $request The request object
     * @return Object Returns a result object containing either error responses or the requested data.
     */
    public function domainAvailability($request);

}
