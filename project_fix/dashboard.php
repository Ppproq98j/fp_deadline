<?php
session_start();
require "database.php";

if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
  header("Location: login.php"); exit();
}
$user = $_SESSION['user'];
$user_id = (int)$user['id'];
$active = 'dashboard';


// stats
$stmt = $conn->prepare("SELECT COUNT(*) as total,
    SUM(CASE WHEN status='sedang' THEN 1 ELSE 0 END) as aktif,
    SUM(CASE WHEN status='selesai' THEN 1 ELSE 0 END) as selesai
    FROM deadlines WHERE user_id = ?");
$stmt->bind_param("i",$user_id); $stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$total = (int)($res['total'] ?? 0);
$aktif = (int)($res['aktif'] ?? 0);
$selesai = (int)($res['selesai'] ?? 0);
$stmt->close();

// deadlines
$stmt = $conn->prepare("SELECT * FROM deadlines WHERE user_id = ? ORDER BY due_date ASC, created_at DESC");
$stmt->bind_param("i",$user_id); $stmt->execute();
$deadlines = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard - Deadline Manager</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<?php include 'components/sidebar.php'; ?>
<?php include 'components/navbar.php'; ?>

<div class="max-w-7xl mx-auto p-6">

  <!-- STATS -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg p-6 shadow">
      <p class="text-gray-500">Total Deadline</p>
      <p class="text-3xl font-semibold mt-2"><?= $total ?></p>
    </div>
    <div class="bg-white rounded-lg p-6 shadow">
      <p class="text-gray-500">Aktif</p>
      <p class="text-3xl font-semibold mt-2 text-blue-600"><?= $aktif ?></p>
    </div>
    <div class="bg-white rounded-lg p-6 shadow">
      <p class="text-gray-500">Selesai</p>
      <p class="text-3xl font-semibold mt-2 text-green-600"><?= $selesai ?></p>
    </div>
  </div>

  <!-- CONTROLS -->
  <div class="bg-white rounded-lg p-6 shadow mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
      <button id="openAddModal" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold">Tambah Deadline Baru</button>
      <button id="testNotif" class="bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold">Tes Notifikasi</button>
      <a href="export_csv.php" class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold inline-block hover:bg-gray-600">Export Data</a>

      <form method="GET" action="" class="flex flex-col sm:flex-row gap-3 w-full sm:ml-auto">
        <input type="text" name="q" placeholder="Cari deadline..." class="border rounded-lg px-4 py-3 w-full sm:w-60" value="<?= isset($_GET['q'])?htmlspecialchars($_GET['q']):'' ?>">
        <select name="status" class="border rounded-lg px-4 py-3">
          <option value="">Semua Status</option>
          <option value="sedang" <?= (isset($_GET['status'])&&$_GET['status']=='sedang')?'selected':'' ?>>Sedang</option>
          <option value="selesai" <?= (isset($_GET['status'])&&$_GET['status']=='selesai')?'selected':'' ?>>Selesai</option>
        </select>
        <select name="priority" class="border rounded-lg px-4 py-3">
          <option value="">Semua Prioritas</option>
          <option value="tinggi" <?= (isset($_GET['priority'])&&$_GET['priority']=='tinggi')?'selected':'' ?>>Tinggi</option>
          <option value="sedang" <?= (isset($_GET['priority'])&&$_GET['priority']=='sedang')?'selected':'' ?>>Sedang</option>
          <option value="rendah" <?= (isset($_GET['priority'])&&$_GET['priority']=='rendah')?'selected':'' ?>>Rendah</option>
        </select>
        <button type="submit" class="bg-gray-200 px-4 py-3 rounded">Filter</button>
      </form>
    </div>
  </div>

  <!-- LIST -->
  <div class="space-y-4">
  <?php if (count($deadlines)===0): ?>
    <div class="bg-white p-6 rounded shadow text-gray-600">Belum ada deadline. Tambahkan menggunakan tombol "Tambah Deadline Baru".</div>
  <?php else: foreach ($deadlines as $d): $json = htmlspecialchars(json_encode($d), ENT_QUOTES, 'UTF-8'); ?>
    <div class="bg-white p-4 rounded shadow flex flex-col sm:flex-row justify-between items-start gap-4">
      <div class="flex-1">
        <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($d['title']) ?></h3>
        <div class="flex gap-2 mt-2 items-center flex-wrap">
          <span class="px-3 py-1 rounded-full text-sm <?= $d['priority']=='tinggi' ? 'bg-red-100 text-red-700' : ($d['priority']=='sedang' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') ?>">
            <?= htmlspecialchars(ucfirst($d['priority'])) ?>
          </span>
          <?php if (!empty($d['category'])): ?>
            <span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700"><?= htmlspecialchars($d['category']) ?></span>
          <?php endif; ?>
          <span class="text-sm text-gray-500">Due: <?= htmlspecialchars($d['due_date']) ?></span>
        </div>
        <?php if (!empty($d['description'])): ?>
          <p class="text-sm text-gray-600 mt-2"><?= nl2br(htmlspecialchars($d['description'])) ?></p>
        <?php endif; ?>
      </div>

      <div class="flex gap-2 items-center">
        <?php if ($d['status'] === 'sedang'): ?>
          <a href="mark_complete.php?id=<?= (int)$d['id'] ?>" class="bg-green-400 text-white px-3 py-2 rounded">✓ Selesai</a>
        <?php else: ?>
          <span class="bg-green-100 text-green-700 px-3 py-2 rounded">Selesai</span>
        <?php endif; ?>

        <button onclick="openEditModal(<?= (int)$d['id'] ?>, <?= $json ?>)" class="bg-gray-400 text-white px-3 py-2 rounded">Edit</button>
        <a href="delete_deadline.php?id=<?= (int)$d['id'] ?>" onclick="return confirm('Hapus deadline ini?');" class="bg-red-400 text-white px-3 py-2 rounded">Hapus</a>
      </div>
    </div>
  <?php endforeach; endif; ?>
  </div>

</div>

<?php include 'components/modals.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
