<?php
session_start();
require "database.php";

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

    // Set session notifikasi
    $_SESSION["success"] = "Deadline berhasil ditambahkan!";

    header("Location: dashboard.php");
    exit();
}
?>
