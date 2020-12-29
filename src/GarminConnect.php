<?php
namespace UtagawaVtt\OAuth1\Client\Server;

use League\OAuth1\Client\Server\Server;
use League\Oauth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Credentials\CredentialsInterface;

class GarminConnect extends Server
{
    private const API_URL = "https://connectapi.garmin.com/oauth-service/oauth/";

    private const AUTH_URL = "https://connect.garmin.com/oauthConfirm";

    /**
     * Get the URL for retrieving temporary credentials.
     *
     * @return string
     */
    public function urlTemporaryCredentials()
    {
        return self::API_URL . 'request_token';
    }

    /**
     * Get the URL for redirecting the resource owner to authorize the client.
     *
     * @return string
     */
    public function urlAuthorization()
    {
        return self::AUTH_URL;
    }

    /**
     * Get the URL for retrieving token credentials.
     *
     * @return string
     */
    public function urlTokenCredentials()
    {
        return self::API_URL . 'access_token';
    }

    public function urlUserDetails()
    {
        return '';      // not implemented
    }

    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
    }

    public function userUid($data, TokenCredentials $tokenCredentials)
    {
    }

    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
    }

    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
    }

    protected function protocolHeader($method, $uri, CredentialsInterface $credentials, array $bodyParameters = [])
    {
        $parameters = array_merge(
            $this->baseProtocolParameters(),
            $this->additionalProtocolParameters(),
            [
                'oauth_token' => $credentials->getIdentifier(),
            ],
            $bodyParameters     // added this, garmin need 'oauth_verifier' in headers
        );

        $this->signature->setCredentials($credentials);

        $parameters['oauth_signature'] = $this->signature->sign(
            $uri,
            array_merge($parameters, $bodyParameters),
            $method
        );

        return $this->normalizeProtocolParameters($parameters);
    }
}
