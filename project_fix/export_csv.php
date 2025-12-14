<?php
session_start();
require "database.php";

if (!isset($_SESSION["user"])) { 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION["user"]["id"];

// Query data
$stmt = $conn->prepare("SELECT title, due_date, priority, category, status FROM deadlines WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Set headers
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Data - Deadline Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #export-table, #export-table * {
                visibility: visible;
            }
            #export-table {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-6xl mx-auto bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Export Data Deadline</h1>
        <p class="text-gray-600">Tanggal: <?= date('d F Y') ?></p>
        <div class="mt-4 flex gap-3">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                🖨️ Cetak
            </button>
            <button onclick="exportToCSV()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                📥 Download CSV
            </button>
            <a href="dashboard.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Kembali
            </a>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="overflow-x-auto">
        <table id="export-table" class="min-w-full divide-y divide-gray-200 border">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">Tanggal Deadline</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">Prioritas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $no = 1;
                while ($row = $result->fetch_assoc()):
                    // Warna berdasarkan prioritas
                    $priority_color = match($row['priority']) {
                        'tinggi' => 'bg-red-100 text-red-800',
                        'sedang' => 'bg-yellow-100 text-yellow-800',
                        'rendah' => 'bg-green-100 text-green-800',
                        default => 'bg-gray-100 text-gray-800'
                    };
                    
                    // Warna berdasarkan status
                    $status_color = $row['status'] == 'selesai' 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-blue-100 text-blue-800';
                        
                    $status_text = $row['status'] == 'selesai' ? 'Selesai' : 'Aktif';
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap border"><?= $no ?></td>
                    <td class="px-6 py-4 whitespace-nowrap border font-medium"><?= htmlspecialchars($row['title']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap border">
                        <?= !empty($row['due_date']) ? date('d/m/Y', strtotime($row['due_date'])) : '-' ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap border">
                        <span class="px-3 py-1 rounded-full text-xs font-medium <?= $priority_color ?>">
                            <?= ucfirst($row['priority']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap border">
                        <?= !empty($row['category']) ? htmlspecialchars($row['category']) : '-' ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap border">
                        <span class="px-3 py-1 rounded-full text-xs font-medium <?= $status_color ?>">
                            <?= $status_text ?>
                        </span>
                    </td>
                </tr>
                <?php 
                $no++;
                endwhile; 
                
                if ($no == 1): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 border">
                        Tidak ada data deadline.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
        <h3 class="font-semibold text-gray-700 mb-2">Ringkasan:</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php
            // Hitung statistik
            $stmt = $conn->prepare("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status = 'sedang' THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN priority = 'tinggi' THEN 1 ELSE 0 END) as tinggi
                FROM deadlines WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stats = $stmt->get_result()->fetch_assoc();
            ?>
            <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-gray-800"><?= $stats['total'] ?? 0 ?></div>
                <div class="text-gray-600 text-sm">Total Deadline</div>
            </div>
            <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-green-600"><?= $stats['selesai'] ?? 0 ?></div>
                <div class="text-gray-600 text-sm">Selesai</div>
            </div>
            <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-blue-600"><?= $stats['aktif'] ?? 0 ?></div>
                <div class="text-gray-600 text-sm">Aktif</div>
            </div>
            <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                <div class="text-2xl font-bold text-red-600"><?= $stats['tinggi'] ?? 0 ?></div>
                <div class="text-gray-600 text-sm">Prioritas Tinggi</div>
            </div>
        </div>
    </div>
</div>

<script>
function exportToCSV() {
    // Ambil data dari tabel
    const rows = [];
    const headers = [];
    
    // Ambil header
    document.querySelectorAll('#export-table thead th').forEach(th => {
        headers.push(th.innerText.trim());
    });
    rows.push(headers);
    
    // Ambil data
    document.querySelectorAll('#export-table tbody tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => {
            // Ambil teks tanpa badge styling
            const badge = td.querySelector('span');
            if (badge) {
                row.push(badge.innerText.trim());
            } else {
                row.push(td.innerText.trim());
            }
        });
        rows.push(row);
    });
    
    // Convert ke CSV
    const csv = rows.map(row => 
        row.map(cell => {
            // Handle nilai kosong
            if (!cell || cell === '-') return '';
            // Escape quotes dan wrap dengan quotes
            return '"' + String(cell).replace(/"/g, '""') + '"';
        }).join(',')
    ).join('\n');
    
    // Download
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'deadlines_export_<?= date("Y-m-d") ?>.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>

</body>
</html>