<?php
require_once __DIR__ . '/vendor/autoload.php';

use Google\Auth\Middleware\AuthTokenMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

$credFileName = "./creds.json"; // CHANGE TO YOU PATH
$jsonKey = json_decode(file_get_contents($credFileName), true);


$creds = new \Google\Auth\OAuth2([
    'audience' => $jsonKey['token_uri'],
    'issuer' => $jsonKey['client_email'],
    'scope' => ['account-management'],
    'signingAlgorithm' => 'RS256',
    'signingKey' => $jsonKey['private_key'],
    'signingKeyId' => $jsonKey['private_key_id'],
    'sub' => null,
    'tokenCredentialUri' => $jsonKey['token_uri'],
]);

$middleware = new AuthTokenMiddleware($creds);
$stack = HandlerStack::create();
$stack->push($middleware);

$client = new Client([
    'handler' => $stack,
    'base_uri' => 'https://datahub-api.garpun.com',
    'auth' => 'google_auth'
]);

$response = $client->post('v1/metaql/query', [
    'json' => [
        "query" => "select id, name from adplatform.client where name is not null",
        "shardKey" => null
    ]
]);

print_r((string)$response->getBody());