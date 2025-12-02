<?php
// navbar.php expects $user variable available
?>
<nav class="bg-white shadow px-4 py-3">
  <div class="max-w-7xl mx-auto flex items-center justify-between">
    <div class="flex items-center gap-3">
      <button id="openSidebar" class="text-3xl" aria-label="Open sidebar">☰</button>
      <h1 class="text-2xl font-bold text-gray-800">Deadline Manager</h1>
    </div>
    <div class="flex items-center gap-4">
      <span class="text-gray-700 hidden sm:inline">Halo, <strong><?= htmlspecialchars($user['fullname'] ?? '') ?></strong></span>
      <a href="logout.php" class="bg-red-500 text-white px-3 py-2 rounded">Logout</a>
    </div>
  </div>
</nav>
