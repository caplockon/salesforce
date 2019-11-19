<?php


namespace Twobes\Salesforce\Authentication;


use Twobes\Salesforce\Http\SalesforceRequest;
use Twobes\Salesforce\SalesforceClient;
use Twobes\Salesforce\SalesforceCredential;
use Twobes\Salesforce\SalesforceException;

class SalesforceAuthenticator implements SalesforceAuthenticatorInterface
{
    /**
     * @var SalesforceAccessToken|null
     */
    protected $accessToken;

    /**
     * @var SalesforceClient
     */
    protected $client;

    /**
     * Authenticate request
     * @param SalesforceRequest $request
     * @throws SalesforceException
     */
    public function authRequest(SalesforceRequest $request)
    {
        $token = $this->getValidToken();
        if ($token instanceof SalesforceAccessToken) {
            $request->setHeader("Authorization", "Bearer {$token->getTokenValue()}");
        }
        else {
            throw new SalesforceException("Could not obtain access token");
        }
    }

    /**
     * Retrieve a valid token to use
     * @return SalesforceAccessToken|null
     * @throws SalesforceException
     */
    protected function getValidToken()
    {
        $accessToken = $this->getCurrentToken();
        if ( !($accessToken instanceof SalesforceAccessToken)) {
            $accessToken = $this->createAccessToken();
            $this->setCurrentToken($accessToken);
        }
        elseif ( $accessToken->isExpired() ) {
            $accessToken = $this->refreshToken($this->accessToken);
            $this->setCurrentToken($accessToken);
        }

        return $accessToken;
    }

    /**
     * Return current token
     * @return SalesforceAccessToken|null
     */
    protected function getCurrentToken()
    {
        return $this->accessToken;
    }

    /**
     * Return current token
     * @param $token
     */
    protected function setCurrentToken($token)
    {
        $this->accessToken = $token;
    }

    /**
     * Create access token
     * @throws SalesforceException
     */
    protected function createAccessToken()
    {
        $credential = $this->client->getCredential();

        if ( !($credential instanceof SalesforceCredential) ) {
            throw new SalesforceException("Credential must be set");
        }

        $tokenUrl = strlen($credential->authUrl) > 0 ? $credential->authUrl : "https://login.salesforce.com/services/oauth2/token";
        $params = "grant_type=password"
            . "&client_id=" . $credential->clientId
            . "&client_secret=" . $credential->clientSecret
            . "&username=" . urlencode( $credential->username )
            . "&password=" . urlencode( $credential->password . $credential->securityToken );

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec( $ch );
        $authData = json_decode( $response, true );
        if ($authData) {
            // Update base uri
            $this->client->setBaseUri($authData['instance_url']);
            return new SalesforceAccessToken($authData['access_token'], new \DateTime());
        }

        return null;
    }

    /**
     * Refresh token
     * @param SalesforceAccessToken $oldToken
     * @return SalesforceAccessToken|null
     * @throws SalesforceException
     */
    protected function refreshToken(SalesforceAccessToken $oldToken)
    {
        return $this->createAccessToken();
    }

    /**
     * Set client
     * @param SalesforceClient $client
     * @return void
     */
    public function setClient(SalesforceClient $client)
    {
        $this->client = $client;
    }
}