<?php


namespace Twobes\Salesforce\Models;


use Twobes\Salesforce\Http\SalesforceRequest;

class Util extends BaseModel
{
    /**
     * Retrieve available versions
     * @return false|array
     */
    public function versions()
    {
        $req = new SalesforceRequest("GET", "/services/data");
        $res = $this->client->sendRequest($req);
        return $res->getBody();
    }

    /**
     * Retrieve list of fields of an object
     * @param $objectName
     * @return false|array
     */
    public function describe($objectName)
    {
        $req = new SalesforceRequest("GET", "/services/data/{$this->client->getVersion()}/sobjects/{$objectName}/describe/");
        $res = $this->client->authAndSendRequest($req);
        return $res->getBody();
    }

    /**
     * Retrieve available objects
     * @return false|array
     */
    public function availableSObjects()
    {
        $req = new SalesforceRequest("GET", "/services/data/{$this->client->getVersion()}/sobjects/");
        $res = $this->client->authAndSendRequest($req);
        return $res->getBody();
    }

    /**
     * Execute a SOQL to retrieve data
     * @param string $query
     * @return false|array
     */
    public function executeSOQL($query)
    {
        $req = new SalesforceRequest("GET", "/services/data/{$this->client->getVersion()}/query?q=" . urlencode($query));
        $res = $this->client->authAndSendRequest($req);
        return $res->getBody();
    }

    /**
     * Fetch resource of Salesforce instance
     * @return false|array
     */
    public function resources()
    {
        $req = new SalesforceRequest("GET", "/services/data/{$this->client->getVersion()}/");
        $res = $this->client->authAndSendRequest($req);
        return $res->getBody();
    }

    /**
     * Retrieve basic information of an object
     * @param string $objectName
     * @return false|array
     */
    public function getBasicObjectInformation($objectName)
    {
        $req = new SalesforceRequest("GET", "/services/data/{$this->client->getVersion()}/sobjects/{$objectName}/");
        $res = $this->client->authAndSendRequest($req);
        return $res->getBody();
    }

    /**
     * Update an object
     * @param string $objectName
     * @param string $objectID
     * @param array $data
     * @return bool
     */
    public function updateObject($objectName, $objectID, $data)
    {
        $req = new SalesforceRequest("PATCH", "/services/data/{$this->client->getVersion()}/sobjects/{$objectName}/{$objectID}");
        $req->setBody($data);
        $res = $this->client->authAndSendRequest($req);
        return $res->getStatusCode() === 204;
    }

    /**
     * Retrieve an object
     * @param string $objectName
     * @param string $objectID
     * @return false|array
     */
    public function getObject($objectName, $objectID)
    {
        $req = new SalesforceRequest("GET", "/services/data/{$this->client->getVersion()}/sobjects/{$objectName}/{$objectID}");
        $res = $this->client->authAndSendRequest($req);
        return $res->getBody();
    }

    /**
     * Create an object
     * @param string $objectName
     * @param array $data
     * @return false|array
     */
    public function createObject($objectName, $data)
    {
        $req = new SalesforceRequest("POST", "/services/data/{$this->client->getVersion()}/sobjects/{$objectName}/");
        $req->setBody($data);
        $res = $this->client->authAndSendRequest($req);
        return $res->getBody();
    }

    /**
     * Delete an object
     * @param string $objectName
     * @param string $objectID
     * @return bool
     */
    public function deleteObject($objectName, $objectID)
    {
        $req = new SalesforceRequest("DELETE", "/services/data/{$this->client->getVersion()}/sobjects/{$objectName}/{$objectID}");
        $res = $this->client->authAndSendRequest($req);
        return $res->getStatusCode() === 200;
    }
}