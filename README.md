# Unofficial garmin connect adapter for Oauth 1.0 Client.

This package provides a Garmin Connect Oauth 1.0 Client for the PHP League's [OAuth 1.0 Client](https://github.com/thephpleague/oauth1-client).

## Installation

```
composer require keosion/garmin-oauth1
```

## Usage

```php
// Instantiate server - we will use it everywhere
$server = new \keosion\OAuth1\Client\Server\GarminConnect([
    'identifier'   => 'your-consumer-id',
    'secret'       => 'your-consumer-secret',
    'callback_uri' => 'http://callback.url'
]);

------ First file ------
// 1st part of OAuth 1.0 authentication : Fetch temporary credentials
$temporaryCredentials = $server->getTemporaryCredentials();
$_SESSION['temporary_credentials'] = serialize($temporaryCredentials);  // Save temporary credentials
session_write_close();

// 2nd part of OAuth 1.0 authentication : Redirect user to the login page
$server->authorize($temporaryCredentials);

------ Second file (callback destination) ------
if (!isset($_GET['oauth_token']) || !isset($_GET['oauth_verifier']) || !isset($_SESSION['temporary_credentials'])) {
    throw new Exception("Need temporary oauth credentials, oauth_token and oauth_verifier to proceed.");
}

// Retrieve temporary credentials we saved before
$temporaryCredentials = unserialize($_SESSION['temporary_credentials']);

// Retrieve token credentials (class League\Oauth1\Client\Credentials\TokenCredentials) from the server
try {
    $tokenCredentials = $server->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);
    // $access_token = $tokenCredentials->getIdentifier();
    // $access_token_secret = $tokenCredentials->getSecret();
} catch (\League\OAuth1\Client\Credentials\CredentialsException $e) {
    // handle exception
}

------ Sign subsequent requests to Garmin Connect API ------
$headers = $server->getHeaders($tokenCredentials, 'GET', $url);

```

Please refer to the Garmin Connect API documentation for the available endpoints.
