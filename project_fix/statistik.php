<?php
session_start();
require "database.php";
if (!isset($_SESSION["user"])) { 
    header("Location: login.php"); 
    exit(); 
}
$user = $_SESSION["user"];
$user_id = (int)$user["id"];
$active = "statistik";

// Pastikan koneksi database tersedia
if (!isset($conn)) {
    die("Koneksi database tidak tersedia");
}

// Hitung statistik dari database
// Total deadline
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM deadlines WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_deadlines = $row['total'] ?? 0;
$stmt->close();

// Deadline aktif (status = 'sedang')
$stmt = $conn->prepare("SELECT COUNT(*) as aktif FROM deadlines WHERE user_id = ? AND status = 'sedang'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$aktif = $row['aktif'] ?? 0;
$stmt->close();

// Deadline selesai (status = 'selesai')
$stmt = $conn->prepare("SELECT COUNT(*) as selesai FROM deadlines WHERE user_id = ? AND status = 'selesai'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$selesai = $row['selesai'] ?? 0;
$stmt->close();

// Distribusi prioritas
$stmt = $conn->prepare("SELECT priority, COUNT(*) as jumlah FROM deadlines WHERE user_id = ? GROUP BY priority");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Inisialisasi array untuk prioritas
$prioritas = ['tinggi' => 0, 'sedang' => 0, 'rendah' => 0];
while ($row = $result->fetch_assoc()) {
    $prioritas[$row['priority']] = $row['jumlah'];
}
$stmt->close();

// Deadline dalam 7 hari ke depan
$stmt = $conn->prepare("SELECT COUNT(*) as deadline_mendatang FROM deadlines 
                       WHERE user_id = ? 
                       AND status = 'sedang' 
                       AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$deadline_mendatang = $row['deadline_mendatang'] ?? 0;
$stmt->close();

// Persentase penyelesaian
$persentase_selesai = $total_deadlines > 0 ? round(($selesai / $total_deadlines) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Statistik - Deadline Manager</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .progress-circle {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: conic-gradient(#3b82f6 <?= $persentase_selesai ?>%, #e5e7eb 0);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .progress-circle::before {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: white;
        }
        .progress-text {
            position: relative;
            z-index: 1;
            text-align: center;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'components/sidebar.php'; ?>
<?php include 'components/navbar.php'; ?>

<div class="max-w-4xl mx-auto mt-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Deadline Manager</h1>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Grafik & Statistik</h2>
    </div>

    <!-- Container utama - 2 kolom sederhana -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kolom Kiri: Progress Penyelesaian -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Progress Penyelesaian</h3>
            
            <!-- Circle Progress -->
            <div class="flex flex-col items-center mb-6">
                <div class="progress-circle mb-4">
                    <div class="progress-text">
                        <div class="text-4xl font-bold text-gray-800"><?= $persentase_selesai ?>%</div>
                        <div class="text-gray-600">Selesai</div>
                    </div>
                </div>
                
                <!-- Stats sederhana -->
                <div class="w-full space-y-3">
                    <div class="text-center">
                        <div class="text-gray-600"><?= $selesai ?> dari <?= $total_deadlines ?> deadline</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-gray-600">7 hari ke depan</div>
                        <div class="font-bold text-gray-800"><?= $deadline_mendatang ?> deadline</div>
                    </div>
                    
                    <div class="text-center text-gray-600 text-sm">
                        <?php 
                        if ($persentase_selesai == 100) {
                            echo "Semua selesai! 🎉";
                        } elseif ($persentase_selesai >= 80) {
                            echo "Sangat baik";
                        } elseif ($persentase_selesai >= 50) {
                            echo "Cukup baik";
                        } else {
                            echo "Perlu peningkatan";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Simple bars -->
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-700">Selesai</span>
                    <span class="font-bold text-gray-800"><?= $selesai ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: <?= $total_deadlines > 0 ? ($selesai / $total_deadlines) * 100 : 0 ?>%"></div>
                </div>
                
                <div class="flex justify-between mt-3">
                    <span class="text-gray-700">Aktif</span>
                    <span class="font-bold text-gray-800"><?= $aktif ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $total_deadlines > 0 ? ($aktif / $total_deadlines) * 100 : 0 ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Distribusi Prioritas -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Distribusi Prioritas</h3>
            
            <!-- Grid 3 kolom untuk prioritas -->
            <div class="grid grid-cols-3 gap-4 mb-8">
                <!-- Tinggi -->
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600 mb-1"><?= $prioritas['tinggi'] ?></div>
                    <div class="text-gray-700 font-medium">Tinggi</div>
                    <div class="text-gray-500 text-sm mt-1"><?= $prioritas['tinggi'] ?> deadline</div>
                </div>
                
                <!-- Sedang -->
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600 mb-1"><?= $prioritas['sedang'] ?></div>
                    <div class="text-gray-700 font-medium">Sedang</div>
                    <div class="text-gray-500 text-sm mt-1"><?= $prioritas['sedang'] ?> deadline</div>
                </div>
                
                <!-- Rendah -->
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 mb-1"><?= $prioritas['rendah'] ?></div>
                    <div class="text-gray-700 font-medium">Rendah</div>
                    <div class="text-gray-500 text-sm mt-1"><?= $prioritas['rendah'] ?> deadline</div>
                </div>
            </div>
            
            <!-- Simple bar chart -->
            <div class="space-y-3">
                <!-- Bar Tinggi -->
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-700">Tinggi</span>
                        <span class="text-red-600 font-bold"><?= $prioritas['tinggi'] ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-red-500 h-3 rounded-full" style="width: <?= $total_deadlines > 0 ? ($prioritas['tinggi'] / $total_deadlines) * 100 : 0 ?>%"></div>
                    </div>
                </div>
                
                <!-- Bar Sedang -->
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-700">Sedang</span>
                        <span class="text-yellow-600 font-bold"><?= $prioritas['sedang'] ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-yellow-500 h-3 rounded-full" style="width: <?= $total_deadlines > 0 ? ($prioritas['sedang'] / $total_deadlines) * 100 : 0 ?>%"></div>
                    </div>
                </div>
                
                <!-- Bar Rendah -->
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-700">Rendah</span>
                        <span class="text-green-600 font-bold"><?= $prioritas['rendah'] ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 h-3 rounded-full" style="width: <?= $total_deadlines > 0 ? ($prioritas['rendah'] / $total_deadlines) * 100 : 0 ?>%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Ringkasan Sederhana -->
            <div class="mt-8 pt-6 border-t">
                <div class="text-gray-700 mb-2">Total Deadline: <span class="font-bold"><?= $total_deadlines ?></span></div>
                <div class="text-gray-700 mb-2">Aktif: <span class="font-bold text-blue-600"><?= $aktif ?></span></div>
                <div class="text-gray-700">Selesai: <span class="font-bold text-green-600"><?= $selesai ?></span></div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>