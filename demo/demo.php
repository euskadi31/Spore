<?php

namespace Demo;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../build/Acme/Client/Github.php';

use Acme;
use Spore;

echo "Demo with generate client" . PHP_EOL;
$client = new Acme\Client\Github;
print_r($client->getUser(array(
    'user' => 'euskadi31'
)));

echo PHP_EOL;

echo "Demo with default client" . PHP_EOL;

$client = new Spore\Client();
$client->loadSpec(__DIR__ . '/spec/github.json');
$response = $client->call('GET', 'get_user', array(
    'user' => 'euskadi31'
));

print_r($response->getContent());