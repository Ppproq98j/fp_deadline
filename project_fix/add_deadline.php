<?php
session_start();
require "database.php";

// Pastikan user login
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_user   = $_POST["user_id"];
    $title     = $_POST["title"];
    $desc      = $_POST["description"];
    $due_date  = $_POST["due_date"];
    $priority  = $_POST["priority"];
    $category  = $_POST["category"];

    // Simpan ke database
    $stmt = $conn->prepare("
        INSERT INTO deadlines (user_id, title, description, due_date, priority, category)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssss", $id_user, $title, $desc, $due_date, $priority, $category);
    $stmt->execute();
    $stmt->close();

    // Jika terhubung dengan Google Calendar, tambahkan event
    if (isset($_SESSION['google_token'])) {
        require 'vendor/autoload.php';

        $client = new Google\Client();
        $client->setAuthConfig('google/credentials.json');
        $client->setAccessToken($_SESSION['google_token']);

        $service = new Google\Service\Calendar($client);

        $event = new Google\Service\Calendar\Event([
            'summary'     => $title,
            'description' => $desc,
            'start'       => ['date' => $due_date],
            'end'         => ['date' => $due_date]
        ]);

        try {
            $service->events->insert('primary', $event);
        } catch (Exception $e) {
            error_log("Google Calendar Error: " . $e->getMessage());
        }
    }

    header("Location: dashboard.php");
    exit();
}
?>
