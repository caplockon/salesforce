<?php


namespace Twobes\Salesforce;


class SalesforceCredential
{
    public $clientId;
    public $clientSecret;
    public $username;
    public $password;
    public $securityToken;
    public $authUrl;

    /**
     * @param array $array
     * @return SalesforceCredential
     */
    public static function createFromArray(array $array)
    {
        $c = new SalesforceCredential();
        $c->clientId = array_key_exists('clientId', $array) ? $array['clientId'] : $c->clientId;
        $c->clientSecret = array_key_exists('clientSecret', $array) ? $array['clientSecret'] : $c->clientSecret;
        $c->username = array_key_exists('username', $array) ? $array['username'] : $c->username;
        $c->password = array_key_exists('password', $array) ? $array['password'] : $c->password;
        $c->securityToken = array_key_exists('securityToken', $array) ? $array['securityToken'] : $c->securityToken;
        $c->authUrl = array_key_exists('authUrl', $array) ? $array['authUrl'] : $c->authUrl;

        return $c;
    }
}