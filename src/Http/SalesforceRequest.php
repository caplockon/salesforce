<?php


namespace Twobes\Salesforce\Http;


class SalesforceRequest
{
    /**
     * @var string Request method
     */
    protected $method;

    /**
     * @var string Request uri
     */
    protected $uri;

    /**
     * @var array Request body
     */
    protected $body = [];

    /**
     * @var array Request headers
     */
    protected $headers = [];

    /**
     * SalesforceRequest constructor.
     * @param string $method Request method
     * @param string $uri Request uri
     * @param array $body Request body
     * @param array $headers Request headers
     */
    public function __construct($method, $uri, $body = [], $headers = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * Returns headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set headers for this request
     * @param array $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Set a header for this request
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Returns uri of this request
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set uri for this request
     * @param string $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Returns body
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set request body
     * @param mixed $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Return request method
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set request method
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }
}