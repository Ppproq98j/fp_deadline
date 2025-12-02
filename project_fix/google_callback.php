<?php
require 'vendor/autoload.php';
session_start();

if (!isset($_GET['code'])) {
    die("Error: No code returned");
}

$client = new Google\Client();
$client->setAuthConfig('google/credentials.json');
$client->setRedirectUri('http://localhost/project_fix/google_callback.php');
$client->addScope(Google\Service\Calendar::CALENDAR);

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    die("Token Error: " . $token['error_description']);
}

$_SESSION['google_token'] = $token;

header("Location: dashboard.php?google=connected");
exit;
