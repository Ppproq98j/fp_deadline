<?php
include "database.php";
session_start();
$user_id = $_SESSION['user_id'];

$q = $conn->query("
    SELECT * FROM notifications
    WHERE user_id = '$user_id'
    ORDER BY created_at DESC
");

$data = [];
while ($row = $q->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
