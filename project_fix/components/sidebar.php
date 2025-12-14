<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$notifTotal = 0;

if (isset($_SESSION['user'])) {
    $user_id = (int)$_SESSION['user']['id'];

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM notifications
        WHERE user_id = ? AND is_read = 0
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $notifTotal = (int)($stmt->get_result()->fetch_assoc()['total'] ?? 0);
    $stmt->close();
}

if (!isset($active)) $active = '';
?>

<div id="overlay"></div>

<aside id="sidebar">
  <div class="sidebar-header">
    <h2>Menu</h2>
    <button id="closeSidebar" class="close-btn" aria-label="Close sidebar">×</button>
  </div>

  <ul class="menu-list">
    <li class="<?= ($active == 'dashboard') ? 'active' : '' ?>">
      <a href="dashboard.php">📊 Dashboard</a>
    </li>

    <li class="<?= ($active == 'kalender') ? 'active' : '' ?>">
      <a href="kalender.php">📅 Kalender</a>
    </li>

    <li class="<?= ($active == 'profil') ? 'active' : '' ?>">
      <a href="profil.php">👤 Profil</a>
    </li>

    <!-- 🔔 NOTIFIKASI -->
    <li class="<?= ($active == 'notif') ? 'active' : '' ?>">
      <a href="notifications.php" class="flex items-center justify-between">
        <span>🔔 Notifikasi</span>

        <?php if ($notifTotal > 0): ?>
          <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
            <?= $notifTotal ?>
          </span>
        <?php endif; ?>
      </a>
    </li>

    <li class="<?= ($active == 'statistik') ? 'active' : '' ?>">
      <a href="statistik.php">📈 Statistik</a>
    </li>
  </ul>
</aside>
