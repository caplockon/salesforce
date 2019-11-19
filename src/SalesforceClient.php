<?php

namespace Twobes\Salesforce;

use Twobes\Salesforce\Authentication\SalesforceAuthenticator;
use Twobes\Salesforce\Authentication\SalesforceAuthenticatorInterface;
use Twobes\Salesforce\Http\SalesforceRequest;
use Twobes\Salesforce\Http\SalesforceResponse;
use Twobes\Salesforce\Models\BaseModel;
use Twobes\Salesforce\Models\Usual\Account;
use Twobes\Salesforce\Models\Usual\Lead;
use Twobes\Salesforce\Models\Usual\Opportunity;
use Twobes\Salesforce\Models\SObjectModel;
use Twobes\Salesforce\Models\Util;

/**
 * Class SalesforceClient
 * @package Salesforce
 *
 * @property-read Lead $lead Lead model
 * @property-read Opportunity $opportunity Opportunity model
 * @property-read Account $account Account model
 * @property-read Util $util Util model
 */
class SalesforceClient
{
    /**
     * Salesforce API version
     * @var string
     */
    protected $version;

    /**
     * Support models
     * @var array
     */
    protected $models = [
        'lead'          => Lead::class,
        'opportunity'   => Opportunity::class,
        'account'       => Account::class,
        'util'          => Util::class,
    ];

    /**
     * Your base instance uri
     * @var string
     */
    protected $baseUri;

    /**
     * Salesfore credentials
     * @var SalesforceCredential
     */
    protected $credential;

    /**
     * Authenticator
     * @var SalesforceAuthenticatorInterface
     */
    protected $authenticator;

    /**
     * Default header of request
     * @var array
     */
    protected $defaultHeaders = [
        'Content-Type' => "application/json"
    ];

    /**
     * SalesforceClient constructor.
     * @param string $baseUri
     * @param SalesforceCredential|array|null $credential
     * @param string|null $version
     */
    public function __construct(string $baseUri, $credential = null, ?string $version = null)
    {
        $this->setBaseUri($baseUri);
        $this->setCredential($credential);
        $this->setVersion($version);
        $this->setAuthenticator(SalesforceConfigs::getSetting('defaultAuthenticator', new SalesforceAuthenticator()));
    }

    /**
     * Set authenticator for this client
     * @param SalesforceAuthenticatorInterface $authenticator
     * @return $this
     */
    public function setAuthenticator(SalesforceAuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;
        $this->authenticator->setClient($this);
        return $this;
    }

    /**
     * Set version for this client
     * @param string $version
     * @return $this
     */
    public function setVersion(?string $version)
    {
        if ( strlen($version) > 0 ) {
            $this->version = $version;
        }
        else {
            $this->version = SalesforceConfigs::getSetting('defaultVersion', "v20.0");
        }

        return $this;
    }

    /**
     * Set credential for client
     * @param SalesforceCredential|array $credential
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        if (is_array($this->credential)) {
            $this->credential = SalesforceCredential::createFromArray($credential);
        }
        elseif ( !($this->credential instanceof SalesforceCredential) ) {
            $this->credential = null;
        }
    }

    /**
     * @param null $baseUri
     */
    public function setBaseUri($baseUri): void
    {
        $this->baseUri = $baseUri;
    }

    /**
     * Magic method to create model
     *
     * @param string $name
     * @return BaseModel
     */
    public function __get($name)
    {
        if (isset($this->models[$name])) {
            $model = $this->models[$name];
            if ( !is_object($model) ) {
                $model = call_user_func_array([$model, "_createModel"], [$this]);
                $this->models[$name] = $model;
            }
            return $model;
        }

        return null;
    }

    /**
     * Create sObject model
     *
     * @param string $name
     * @return SObjectModel
     */
    public function sObjectModel($name)
    {
        $model = SObjectModel::_createModel($this);
        $model->setObjectName($name);
        return $model;
    }

    /**
     * Send request to Salesforce
     * @param SalesforceRequest $request
     * @return SalesforceResponse
     */
    public function sendRequest(SalesforceRequest $request)
    {
        // Build uri
        $uri = $this->buildUri($request->getUri());
        // Combine default headers and build request headers
        $headers = Helper::buildHeaders(array_merge($this->defaultHeaders, $request->getHeaders()));
        // Assign method
        $method = strtoupper($request->getMethod());

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $verboseHandler = self::verboseHandler();
        if ($verboseHandler !== false) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_STDERR, $verboseHandler);
        }

        // Set request body
        if ( in_array($method, ["POST", "PATCH", "PUT", "OPTIONS"]) ) {
            $postFields = json_encode($request->getBody());
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }

        $res = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        // If verbose handler is initialized, then unset it
        if ($verboseHandler !== false) {
            fclose($verboseHandler);
        }

        $res = Helper::parseResponse($info['header_size'], $res);
        return new SalesforceResponse(
            $info['http_code'],
            $res['body'],
            $res['headers']
        );
    }

    /**
     * Send request to Salesforce - authenticated
     *
     * @param SalesforceRequest $request
     * @return SalesforceResponse
     */
    public function authAndSendRequest(SalesforceRequest $request)
    {
        $this->authenticator->authRequest($request);
        return $this->sendRequest($request);
    }

    /**
     * Check to see if http debug mode is turned on
     * @return false|resource
     */
    protected static function verboseHandler()
    {
        $verboseFile = SalesforceConfigs::getSetting('httpRequestVerboseLogFile', false);
        $verboseHandler = false;
        if ($verboseFile !== false && strlen($verboseFile) > 0) {
            $verboseHandler = fopen($verboseFile, 'w+');
        }
        return $verboseHandler;
    }

    /**
     * Returns base uri (instance uri)
     * @return string
     */
    protected function baseUri()
    {
        return $this->baseUri;
    }

    /**
     * Build full uri
     * @param string $path
     * @return string
     */
    protected function buildUri($path)
    {
        return $this->baseUri() . $path;
    }

    /**
     * Returns credential of this client
     * @return SalesforceCredential|null
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * Returns version of this client
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}