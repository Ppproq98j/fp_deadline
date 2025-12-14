<?php
session_start();
require "database.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: notifications.php");
    exit();
}

$user_id  = (int)$_SESSION['user']['id'];
$notif_id = (int)$_GET['id'];

// pastikan notif milik user yang login
$stmt = $conn->prepare("
    UPDATE notifications 
    SET is_read = 1 
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $notif_id, $user_id);
$stmt->execute();
$stmt->close();

header("Location: notifications.php");
exit();
