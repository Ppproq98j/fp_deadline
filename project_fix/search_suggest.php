<?php
require "database.php";
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user']['id'];

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$q = $_GET['q'];
$search = "%$q%";

$stmt = $conn->prepare("SELECT id, title FROM deadlines 
                        WHERE user_id = ? AND title LIKE ?
                        ORDER BY title LIMIT 5");
$stmt->bind_param("is", $user_id, $search);
$stmt->execute();

$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($data);
