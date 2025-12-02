<?php
session_start();
require "database.php";
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: dashboard.php"); exit(); }

$id = (int) $_POST['id'];
$title = trim($_POST['title']);
$description = trim($_POST['description']);
$due_date = $_POST['due_date'];
$priority = $_POST['priority'];
$category = trim($_POST['category']);

if ($title === '' || $due_date === '') {
    $_SESSION['error'] = 'Judul dan tanggal wajib diisi.';
    header("Location: dashboard.php"); exit();
}

$stmt = $conn->prepare("UPDATE deadlines SET title=?, description=?, due_date=?, priority=?, category=? WHERE id=?");
$stmt->bind_param("sssssi", $title, $description, $due_date, $priority, $category, $id);
if ($stmt->execute()) {
    header("Location: dashboard.php?success=updated");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
