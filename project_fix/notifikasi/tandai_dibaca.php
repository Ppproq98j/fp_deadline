<?php
include "database.php";

$id = $_POST['id'];

$conn->query("UPDATE notifications SET is_read = 1 WHERE id = '$id'");
echo "OK";
