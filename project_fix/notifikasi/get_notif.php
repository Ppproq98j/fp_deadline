<?php
include "database.php";
session_start();
$user_id = $_SESSION['user_id'];

$q = $conn->query("
    SELECT * FROM notifications 
    WHERE user_id = '$user_id' AND is_read = 0
    ORDER BY created_at DESC
");

$notif = [];
while ($row = $q->fetch_assoc()) {
    $notif[] = $row;
}

echo json_encode($notif);
