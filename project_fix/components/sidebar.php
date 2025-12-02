<?php if (!isset($active)) $active = ''; ?>
<div id="overlay"></div>

<aside id="sidebar">
  <div class="sidebar-header">
    <h2>Menu</h2>
    <button id="closeSidebar" class="close-btn" aria-label="Close sidebar">×</button>
  </div>

  <ul class="menu-list">
    <li class="<?= ($active == 'dashboard') ? 'active' : '' ?>"><a href="dashboard.php">📊 Dashboard</a></li>
    <li class="<?= ($active == 'kalender') ? 'active' : '' ?>"><a href="kalender.php">📅 Kalender</a></li>
    <li class="<?= ($active == 'profil') ? 'active' : '' ?>"><a href="profil.php">👤 Profil</a></li>
    <li class="<?= ($active == 'notif') ? 'active' : '' ?>"><a href="notif.php">🔔 Notifikasi</a></li>
    <li class="<?= ($active == 'statistik') ? 'active' : '' ?>"><a href="statistik.php">📈 Statistik</a></li>
  </ul>
</aside>