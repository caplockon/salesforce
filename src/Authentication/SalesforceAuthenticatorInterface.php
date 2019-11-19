<?php


namespace Twobes\Salesforce\Authentication;


use Twobes\Salesforce\Http\SalesforceRequest;
use Twobes\Salesforce\SalesforceClient;

interface SalesforceAuthenticatorInterface
{
    /**
     * Authenticate Salesforce request
     *
     * @param SalesforceRequest $request
     * @return void
     */
    public function authRequest(SalesforceRequest $request);

    /**
     * Set client
     * @param SalesforceClient $client
     * @return void
     */
    public function setClient(SalesforceClient $client);
}