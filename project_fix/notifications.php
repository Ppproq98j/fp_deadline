<?php
session_start();
require "database.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user']['id'];

/* unread count */
$unreadStmt = $conn->prepare("
    SELECT COUNT(*) total 
    FROM notifications 
    WHERE user_id=? AND is_read=0
");
$unreadStmt->bind_param("i", $user_id);
$unreadStmt->execute();
$unread = $unreadStmt->get_result()->fetch_assoc()['total'];

/* all notifications */
$stmt = $conn->prepare("
    SELECT * FROM notifications
    WHERE user_id=?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifs = $stmt->get_result();
?>

<!doctype html>
<html lang="id">

<head>
<meta charset="utf-8">
<title>Notifikasi</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://cdn.tailwindcss.com"></script>

<!-- WAJIB -->
<link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-gray-100 min-h-screen">

<?php $active='notif'; include 'components/sidebar.php'; ?>
<?php include 'components/navbar.php'; ?>

<div class="max-w-5xl mx-auto p-6">

  <div class="bg-white rounded-2xl shadow p-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold">Riwayat Notifikasi</h1>
        <p class="text-gray-500">
          Anda memiliki <?= $unread ?> notifikasi belum dibaca
        </p>
      </div>

    <a href="mark_all_read.php"
   class="bg-blue-600 text-white px-5 py-2 rounded-lg">
   Tandai Semua Dibaca
</a>

    </div>

    <!-- KOSONG -->
    <?php if ($notifs->num_rows === 0): ?>
      <div class="text-center py-16">
        <div class="text-6xl mb-4">🔔</div>
        <h2 class="text-xl font-semibold">Belum ada notifikasi</h2>
        <p class="text-gray-500">Notifikasi akan muncul di sini</p>
      </div>
    <?php endif; ?>

    <!-- LIST NOTIF -->
    <?php while ($n = $notifs->fetch_assoc()): ?>
      <div class="flex justify-between items-center p-5 mb-4 rounded-xl
        <?= $n['is_read']
            ? 'bg-gray-100 border-l-4 border-gray-400'
            : 'bg-blue-50 border-l-4 border-blue-500'
        ?>">

        <div>
          <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <span class="w-2 h-2 rounded-full
              <?= $n['is_read'] ? 'bg-gray-400' : 'bg-blue-500' ?>">
            </span>
            <?= date('d F Y \p\u\k\u\l H.i', strtotime($n['created_at'])) ?>
          </div>

          <p class="font-semibold text-lg">
            <?= htmlspecialchars($n['title']) ?>
          </p>

          <p class="text-gray-700">
            <?= htmlspecialchars($n['message']) ?>
          </p>
        </div>

        <?php if (!$n['is_read']): ?>
          <a href="mark_read.php?id=<?= $n['id'] ?>"
             class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
             Tandai Dibaca
          </a>
        <?php endif; ?>

      </div>
    <?php endwhile; ?>

  </div>
</div>

<script src="assets/js/main.js"></script>

</body>
</html>
