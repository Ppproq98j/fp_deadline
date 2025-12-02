<?php
require 'vendor/autoload.php';
session_start();

$client = new Google\Client();
$client->setAuthConfig('google/credentials.json');
$client->setRedirectUri('http://localhost/project_fix/google_callback.php');
$client->addScope(Google\Service\Calendar::CALENDAR);
$client->setPrompt('consent');
$client->setAccessType('offline');

$authUrl = $client->createAuthUrl();
header("Location: $authUrl");
exit;
