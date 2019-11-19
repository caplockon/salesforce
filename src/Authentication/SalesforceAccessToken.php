<?php


namespace Twobes\Salesforce\Authentication;


use Twobes\Salesforce\SalesforceConfigs;

class SalesforceAccessToken
{
    /**
     * @var string Token string
     */
    protected $tokenValue;

    /**
     * @var \DateTimeInterface The moment token is created
     */
    protected $createdAt;

    /**
     * SalesforceAccessToken constructor.
     * @param string $tokenValue Token string
     * @param \DateTimeInterface $createdAt The moment token is created
     */
    public function __construct($tokenValue, \DateTimeInterface $createdAt)
    {
        $this->tokenValue = $tokenValue;
        $this->createdAt = $createdAt;
    }

    /**
     * Returns token string
     * @return string
     */
    public function getTokenValue()
    {
        return $this->tokenValue;
    }

    /**
     * Check to see if token is expired
     * @return bool
     */
    public function isExpired()
    {
        return (time() - $this->createdAt->getTimestamp()) > SalesforceConfigs::getSetting('tokenLifeTime', 5400);
    }
}