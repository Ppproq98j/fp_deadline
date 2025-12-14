<?php
$host = "localhost";
$user = "root"; 
$pass = "anwar291221"; 
$dbname = "deadline_app";
$port = 3306;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
