<?php
session_start();

// Hapus token Google
if (isset($_SESSION['google_token'])) {
    unset($_SESSION['google_token']);
}

// Redirect ke halaman kalender setelah disconnect
header("Location: kalender.php?status=disconnected");
exit();
