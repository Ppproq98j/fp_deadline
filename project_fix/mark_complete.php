<?php
session_start();
require "database.php";
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
if (!isset($_GET['id'])) { header("Location: dashboard.php"); exit(); }

$id = (int) $_GET['id'];
$stmt = $conn->prepare("UPDATE deadlines SET status='selesai' WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: dashboard.php?success=done");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
