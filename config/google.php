<?php

require_once __DIR__ . '/../vendor/autoload.php';

$clientID = '305110859372-2rk6kka7p91qlipt831d8l36a5gaaq3c.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/lama/view/pages/landing-page.php';

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
