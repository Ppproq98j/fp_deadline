<?php
session_start();
require "database.php";
if (!isset($_SESSION["user"])) { header("Location: login.php"); exit(); }
$user = $_SESSION["user"];
$user_id = (int)$user["id"];
$active = "kalender";

/* ===============================
   FIX NAVIGASI BULAN
   =============================== */
$month = isset($_GET["month"]) ? (int)$_GET["month"] : date("n");
$year  = isset($_GET["year"]) ? (int)$year = $_GET["year"] : date("Y");

if ($month < 1) { 
    $month = 12; 
    $year--; 
} 
if ($month > 12) { 
    $month = 1; 
    $year++; 
}

$firstDay = mktime(0,0,0,$month,1,$year);
$totalDays = date("t", $firstDay);
$startWeek = date("w", $firstDay);

$monthsIndo = [
    1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",
    7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
];

/* ===============================
   LOAD DEADLINE
   =============================== */
$stmt = $conn->prepare("SELECT id,title,priority,due_date,status FROM deadlines 
                        WHERE user_id=? AND MONTH(due_date)=? AND YEAR(due_date)=?");
$stmt->bind_param("iii", $user_id, $month, $year);
$stmt->execute();
$res = $stmt->get_result();

$deadlineHari = [];
while ($row = $res->fetch_assoc()) {
    $day = (int)date("j", strtotime($row["due_date"]));
    $deadlineHari[$day][] = $row;
}
$stmt->close();

$selectedDay = isset($_GET["day"]) ? (int)$_GET["day"] : null;
$selectedList = ($selectedDay && isset($deadlineHari[$selectedDay])) ? $deadlineHari[$selectedDay] : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kalender - Deadline Manager</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-100 min-h-screen">

<?php include 'components/sidebar.php'; ?>
<?php include 'components/navbar.php'; ?>

<div class="max-w-5xl mx-auto mt-6 bg-white p-6 rounded-xl shadow">

  <!-- NAVIGASI BULAN -->
  <div class="flex justify-between items-center mb-6">

      <?php 
      $prevMonth = $month - 1;
      $prevYear  = $year;
      if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

      $nextMonth = $month + 1;
      $nextYear  = $year;
      if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
      ?>

      <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" 
         class="px-4 py-2 bg-gray-600 text-white rounded-lg">← Bulan Lalu</a>

      <h2 class="text-3xl font-bold text-gray-800">
          <?= $monthsIndo[$month] . " " . $year ?>
      </h2>

      <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" 
         class="px-4 py-2 bg-gray-600 text-white rounded-lg">Bulan Depan →</a>
  </div>
  <div class="mb-4 flex justify-end">
    <a href="https://calendar.google.com/calendar/u/0/r" target="_blank"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg">
        🔗 Buka Google Calendar
    </a>
  </div>

  <!-- KALENDER GRID -->
  <div class="grid grid-cols-7 text-center font-semibold text-gray-600 mb-3">
    <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
  </div>

  <div class="grid grid-cols-7 gap-3">

    <?php for ($i=0; $i < $startWeek; $i++): ?>
      <div></div>
    <?php endfor; ?>

    <?php for ($day = 1; $day <= $totalDays; $day++): 
        $hasDeadline = isset($deadlineHari[$day]);
        $isSelected  = ($selectedDay == $day);
    ?>
      <a href="?month=<?= $month ?>&year=<?= $year ?>&day=<?= $day ?>"
         class="border rounded-xl p-6 text-center relative 
         <?= $isSelected ? 'bg-blue-500 text-white' : 'bg-white' ?>">

        <span class="text-lg font-semibold"><?= $day ?></span>

        <?php if ($hasDeadline): ?>
            <?php 
            $prio = $deadlineHari[$day][0]["priority"];
            $color = $prio == 'tinggi' ? 'bg-red-500' :
                     ($prio == 'sedang' ? 'bg-yellow-500' : 'bg-gray-500');
            ?>
            <span class="absolute bottom-2 left-1/2 -translate-x-1/2 text-xl">
                <span class="w-3 h-3 rounded-full inline-block <?= $color ?>"></span>
            </span>
        <?php endif; ?>
      </a>
    <?php endfor; ?>

  </div>

  <div class="bg-gray-100 p-4 rounded-lg mt-6 text-gray-700">
    <strong>Keterangan:</strong> Tanggal dengan titik memiliki deadline. Klik tanggal untuk melihat detail.
  </div>
</div>

<!-- DETAIL DEADLINE -->
<?php if ($selectedDay): ?>
<div class="max-w-5xl mx-auto mt-6 bg-white p-6 rounded-xl shadow">
  <h2 class="text-2xl font-bold mb-4">
      Deadline pada <?= $selectedDay . " " . $monthsIndo[$month] . " " . $year ?>
  </h2>

  <?php foreach ($selectedList as $d): ?>
    <div class="border-l-4 border-yellow-400 bg-gray-50 p-4 rounded mb-3 flex justify-between items-center">
      <div><p class="font-semibold text-lg"><?= htmlspecialchars($d['title']) ?></p></div>

      <div class="flex gap-2">
        <?php if ($d['status'] == 'sedang'): ?>
          <a href="mark_complete.php?id=<?= $d['id'] ?>" class="bg-green-500 text-white px-4 py-2 rounded">✓ Selesai</a>
        <?php endif; ?>

        <a href="edit_deadline.php?id=<?= $d['id'] ?>" class="bg-gray-500 text-white px-4 py-2 rounded">Edit</a>

        <a onclick="return confirm('Hapus?')" 
           href="delete_deadline.php?id=<?= $d['id'] ?>" 
           class="bg-red-500 text-white px-4 py-2 rounded">Hapus</a>
      </div>
    </div>
  <?php endforeach; ?>

</div>
<?php endif; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
