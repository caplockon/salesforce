<?php


namespace Twobes\Salesforce\Models;


use Twobes\Salesforce\SalesforceClient;

abstract class BaseModel
{
    /**
     * @var SalesforceClient
     */
    protected $client;

    /**
     * Create model
     * @param SalesforceClient $client
     * @return static
     */
    final public static function _createModel(SalesforceClient $client)
    {
        $instance = new static();
        $instance->client = $client;
        return $instance;
    }
}