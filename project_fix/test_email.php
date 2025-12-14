<?php
require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP config (PAKAI YANG SAMA DENGAN AUTO REMINDER)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = '089647739129aa@gmail.com';
    $mail->Password   = 'jsda fzja lgxk mrwj'; // app password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Pengirim & penerima
    $mail->setFrom('089647739129aa@gmail.com', 'Test Deadline App');
    $mail->addAddress('ipin070605@gmail.com'); // ganti ke email kamu

    // Konten email
    $mail->isHTML(true);
    $mail->Subject = '✅ TEST EMAIL - Deadline App';
    $mail->Body    = '
        <h2>Test Email Berhasil 🎉</h2>
        <p>Kalau email ini masuk, berarti:</p>
        <ul>
            <li>PHPMailer ✅</li>
            <li>SMTP Gmail ✅</li>
            <li>App Password ✅</li>
        </ul>
        <p><b>Waktu kirim:</b> '.date('Y-m-d H:i:s').'</p>
    ';

    $mail->send();
    echo "✅ EMAIL TEST BERHASIL TERKIRIM";

} catch (Exception $e) {
    echo "❌ GAGAL KIRIM EMAIL<br>";
    echo "Error: {$mail->ErrorInfo}";
}
