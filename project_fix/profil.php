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

<div class="max-w-4xl mx-auto mt-10 mb-10">

    <!-- HEADER -->
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Profil Pengguna</h1>
        <p class="text-gray-600 mt-1">Kelola informasi akun dan integrasi Google Calendar</p>
    </div>

    <!-- CARD PROFIL -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8 border border-gray-200">

        <h2 class="text-xl font-semibold text-gray-800 mb-6">Profil Pengguna</h2>

        <!-- Nama -->
        <div class="mb-6">
            <label class="block mb-1 text-gray-700 font-medium">Nama Lengkap</label>
            <input 
                type="text"
                class="w-full border p-3 rounded-lg bg-gray-50"
                value="<?= htmlspecialchars($user['fullname']) ?>"
                disabled
            >
        </div>

        <!-- Email -->
        <div class="mb-6">
            <label class="block mb-1 text-gray-700 font-medium">Email</label>
            <input 
                type="email"
                class="w-full border p-3 rounded-lg bg-gray-50"
                value="<?= htmlspecialchars($user['email']) ?>"
                disabled
            >
        </div>

        <!-- NOTIFIKASI (DI BAWAH EMAIL) -->
        <div class="mt-8">
            <h3 class="font-semibold text-gray-900 mb-4">Pengaturan Notifikasi</h3>

            <div class="flex items-center gap-3 mb-6">
                <input type="checkbox" id="notif-toggle" class="w-5 h-5">
                <label for="notif-toggle" class="text-gray-700">
                    Aktifkan notifikasi deadline
                </label>
            </div>

            <button class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                Simpan Perubahan
            </button>
        </div>

    </div>

    <!-- CARD GOOGLE CALENDAR -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Integrasi Google Calendar</h2>
                <p class="text-gray-600 text-sm mt-1">Sinkronkan deadline Anda dengan Google Calendar</p>
            </div>

            <?php if (isset($_SESSION['google_token'])): ?>
                <span class="px-4 py-1 rounded-full bg-green-100 text-green-700 text-sm font-medium">✓ Terhubung</span>
            <?php else: ?>
                <span class="px-4 py-1 rounded-full bg-red-100 text-red-700 text-sm font-medium">✗ Belum Terhubung</span>
            <?php endif; ?>
        </div>

        <?php if (!isset($_SESSION['google_token'])): ?>

        <div class="border border-gray-200 rounded-xl bg-gray-50 p-7 text-center">

            <div class="flex justify-center mb-5">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-3xl">
                    <i class="fa-solid fa-calendar"></i>
                </div>
            </div>

            <p class="text-gray-700 mb-6 max-w-lg mx-auto">
                Hubungkan akun Anda untuk menyinkronkan deadline secara otomatis ke Google Calendar 
                dan menerima pengingat pintar.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                <!-- Fitur 1 -->
                <div class="p-4 bg-white border rounded-lg shadow-sm">
                    <i class="fa-solid fa-sync text-blue-600 text-xl mb-2"></i>
                    <p class="font-medium text-gray-800">Sinkronisasi Otomatis</p>
                </div>

                <!-- Fitur 2 -->
                <div class="p-4 bg-white border rounded-lg shadow-sm">
                    <i class="fa-solid fa-bell text-blue-600 text-xl mb-2"></i>
                    <p class="font-medium text-gray-800">Reminder Pintar</p>
                </div>

                <!-- Fitur 3 -->
                <div class="p-4 bg-white border rounded-lg shadow-sm">
                    <i class="fa-solid fa-globe text-blue-600 text-xl mb-2"></i>
                    <p class="font-medium text-gray-800">Akses Universal</p>
                </div>

            </div>

            <a href="google_connect.php" 
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
               Hubungkan dengan Google
            </a>

            <p class="text-gray-500 text-sm mt-3">
                Aman dan terenkripsi. Kami tidak menyimpan kredensial Google Anda.
            </p>

        </div>

        <?php else: ?>

        <div class="p-5 bg-green-50 border border-green-200 rounded-xl flex justify-between items-center">
            <span class="text-green-700 font-medium">
                Akun Anda sudah terhubung dengan Google Calendar.
            </span>
            <a href="google_disconnect.php" 
               class="bg-red-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-600">
               Putuskan
            </a>
        </div>

        <?php endif; ?>

    </div>

</div>

<script src="assets/js/main.js"></script>
</body>
</html>
