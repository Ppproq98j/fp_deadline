<?php
include "database.php";
session_start();

$user_id = $_SESSION['user_id'];

$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = '$user_id'");
echo "OK";
