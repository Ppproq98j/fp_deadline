<?php
include "database.php";

function tambahNotifikasi($user_id, $pesan) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at)
                            VALUES (?, ?, 0, NOW())");
    $stmt->bind_param("is", $user_id, $pesan);
    $stmt->execute();
}

// Contoh pemanggilan
// tambahNotifikasi($user_id, "Deadline tugas besok! Jangan lupa diselesaikan.");
