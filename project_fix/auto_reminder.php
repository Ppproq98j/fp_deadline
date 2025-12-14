<?php
require "database.php";
require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Jakarta');
$today = date("Y-m-d");
$tomorrow = date("Y-m-d", strtotime("+1 day"));

// Ambil deadline yang perlu diingatkan
$sql = "SELECT d.*, u.email
        FROM deadlines d
        JOIN users u ON d.user_id = u.id
        WHERE d.status = 'sedang'
        AND (
            (d.due_date = ? AND d.reminder_sent_h0 = 0)
            OR
            (d.due_date = ? AND d.reminder_sent_h1 = 0)
        )";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $today, $tomorrow);
$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($data as $d) {

    $mail = new PHPMailer(true);

    try {
        // SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '089647739129aa@gmail.com';
        $mail->Password = 'jsda fzja lgxk mrwj';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->Timeout = 10;

        // Penerima
        $mail->setFrom('089647739129aa@gmail.com', 'Deadline Reminder');
        $mail->addAddress($d['email']);

        $judul = $d['title'];
        $tgl   = $d['due_date'];

        // ================= H-1 =================
        if ($d['due_date'] == $tomorrow && $d['reminder_sent_h1'] == 0) {

            $mail->Subject = "📌 Reminder H-1: $judul";
            $mail->Body    = "Besok adalah batas waktu tugas: $judul\nDeadline: $tgl.";

            if ($mail->send()) {

                // tandai email terkirim
                $update = $conn->prepare(
                    "UPDATE deadlines SET reminder_sent_h1 = 1 WHERE id = ?"
                );
                $update->bind_param("i", $d['id']);
                $update->execute();

                // 🔔 SIMPAN NOTIFIKASI APLIKASI
                $msg = "Deadline \"$judul\" jatuh BESOK ($tgl)";
                $notif = $conn->prepare("
                    INSERT INTO notifications (user_id, deadline_id, title, message, type)
                    VALUES (?, ?, ?, ?, 'h1')
                ");
                $notif->bind_param(
                    "iiss",
                    $d['user_id'],
                    $d['id'],
                    $judul,
                    $msg
                );
                $notif->execute();
            }
        }

        // ================= H-0 =================
        if ($d['due_date'] == $today && $d['reminder_sent_h0'] == 0) {

            $mail->Subject = "⏰ Reminder Hari-H: $judul";
            $mail->Body    = "Hari ini adalah deadline tugas: $judul\nDeadline: $tgl.";

            if ($mail->send()) {

                $update = $conn->prepare(
                    "UPDATE deadlines SET reminder_sent_h0 = 1 WHERE id = ?"
                );
                $update->bind_param("i", $d['id']);
                $update->execute();

                // 🔔 SIMPAN NOTIFIKASI APLIKASI
                $msg = "Deadline \"$judul\" jatuh HARI INI!";
                $notif = $conn->prepare("
                    INSERT INTO notifications (user_id, deadline_id, title, message, type)
                    VALUES (?, ?, ?, ?, 'h0')
                ");
                $notif->bind_param(
                    "iiss",
                    $d['user_id'],
                    $d['id'],
                    $judul,
                    $msg
                );
                $notif->execute();
            }
        }

    } catch (Exception $e) {
        // optional log
    }
}

echo "Reminder selesai dijalankan.\n";
$conn->close();
exit;
