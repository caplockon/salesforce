<?php


namespace Twobes\Salesforce\Http;


class SalesforceResponse
{
    /**
     * @var array Response header
     */
    protected $headers = [];

    /**
     * @var mixed Response body
     */
    protected $body;

    /**
     * @var int Response status code
     */
    protected $statusCode = 200;

    /**
     * SalesforceResponse constructor.
     * @param int $statusCode Response status code
     * @param mixed|null $body Response body
     * @param array $headers Response header
     */
    public function __construct($statusCode, $body = null, $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * Returns all headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return body
     * @return mixed|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return status code
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Get a response header by name
     * @param string $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getHeader($name, $default = null)
    {
        return array_key_exists($name, $this->headers) ? $this->headers[$name] : $default;
    }
}