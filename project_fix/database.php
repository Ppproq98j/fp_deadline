<?php
$host = "localhost";
$user = "root"; 
$pass = ""; 
$dbname = "deadline_app";
$port = 3307;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
