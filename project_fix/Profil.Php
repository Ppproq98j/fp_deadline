<?php
session_start();
require "database.php";
if (!isset($_SESSION["user"])) { 
    header("Location: login.php"); 
    exit(); 
}
$user = $_SESSION["user"];
$active = "profil";

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'components/sidebar.php'; ?>
<?php include 'components/navbar.php'; ?>

<div class="max-w-4xl mx-auto mt-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Deadline Manager</h1>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Profil Pengguna</h2>
    </div>

    <!-- Profil Data -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-semibold text-gray-700 mb-2 text-lg">Nama Lengkap</h3>
        <div class="w-full p-3 border rounded-lg bg-gray-50 mb-6">
            <?= htmlspecialchars($user['fullname']) ?>
        </div>

        <h3 class="font-semibold text-gray-700 mb-2 text-lg">Email</h3>
        <div class="w-full p-3 border rounded-lg bg-gray-50">
            <?= htmlspecialchars($user['email']) ?>
        </div>
    </div>

    <!-- Pengaturan Notifikasi -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Pengaturan Notifikasi</h3>
        
        <div class="flex items-center gap-3 mb-6">
            <input type="checkbox" id="notif-toggle" class="w-5 h-5" checked>
            <label for="notif-toggle" class="text-gray-700">Aktifkan notifikasi deadline</label>
        </div>

        <button class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
            Simpan Perubahan
        </button>
    </div>

    <!-- Integrasi Google Calendar -->
    <div class="bg-white rounded-xl shadow p-6">
        <!-- Header Integrasi -->
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-800">Integrasi Google Calendar</h3>
            <p class="text-gray-600 mt-1">Sinkronkan deadline Anda dengan Google Calendar</p>
        </div>

        <!-- Status Koneksi -->
        <div class="mb-8">
            <?php if (isset($_SESSION['google_token'])): ?>
                <div class="flex gap-3 items-center">
                    <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg font-medium">
                        ✓ Terhubung ke Google Calendar
                    </span>
                    <a href="google_disconnect.php" 
                       class="bg-red-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-600 transition">
                       ✕ Putuskan
                    </a>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-2">
                    <span class="text-red-500 text-2xl">✗</span>
                    <span class="text-gray-700">Belum Terhubung</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Daftar Fitur -->
        <div class="space-y-4 mb-8">
            <!-- Fitur 1 -->
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Hubungkan dengan Google Calendar</h4>
                <p class="text-gray-600 text-sm">Sinkronkan semua deadline Anda ke Google Calendar secara otomatis dan dapatkan reminder langsung di perangkat Anda</p>
            </div>

            <!-- Fitur 2 -->
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Sinkronisasi Otomatis</h4>
                <p class="text-gray-600 text-sm">Notifikasi selalu tersinkron dengan perubahan deadline</p>
            </div>

            <!-- Fitur 3 -->
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Reminder Pintar</h4>
                <p class="text-gray-600 text-sm">Notifikasi di waktu yang tepat sebelum deadline</p>
            </div>

            <!-- Fitur 4 -->
            <div>
                <h4 class="font-semibold text-gray-800 mb-1">Akses Universal</h4>
                <p class="text-gray-600 text-sm">Akses deadline Anda di mana saja dengan Google Calendar</p>
            </div>
        </div>

        <!-- Tombol Hubungkan -->
        <?php if (!isset($_SESSION['google_token'])): ?>
            <div class="pt-6 border-t">
                <a href="google_connect.php" 
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition mb-2">
                   Hubungkan dengan Google Calendar
                </a>
                <p class="text-gray-600 text-sm">
                   Akses untuk terhubung dengan Google Calendar dan sinkronkan semua deadline Anda
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>