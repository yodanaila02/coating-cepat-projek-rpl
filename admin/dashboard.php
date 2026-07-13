<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR ADMIN — DASHBOARD
 * Kartu statistik: Total Booking, Booking Hari Ini,
 * Booking Pending, Booking Selesai, Total Testimoni,
 * Total Galeri + grafik booking 12 bulan terakhir (Chart.js).
 * =====================================================
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

/* --- Statistik kartu --- */
$total_booking  = (int)$db->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
$st = $db->prepare('SELECT COUNT(*) FROM bookings WHERE booking_date = ?');
$st->execute([date('Y-m-d')]);
$booking_today  = (int)$st->fetchColumn();
$booking_pending = (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$booking_done    = (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'done'")->fetchColumn();
$total_testi     = (int)$db->query('SELECT COUNT(*) FROM testimonials')->fetchColumn();
$total_gallery   = (int)$db->query('SELECT COUNT(*) FROM gallery')->fetchColumn();

/* --- Data grafik: jumlah booking per bulan, 12 bulan terakhir --- */
$labels = [];
$values = [];
$bulan_id = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
$st = $db->prepare('SELECT COUNT(*) FROM bookings WHERE DATE_FORMAT(booking_date, "%Y-%m") = ?');
for ($i = 11; $i >= 0; $i--) {
    $t = strtotime("first day of -{$i} month");
    $labels[] = $bulan_id[(int)date('n', $t)] . ' ' . date('y', $t);
    $st->execute([date('Y-m', $t)]);
    $values[] = (int)$st->fetchColumn();
}

$admin_title  = 'Dashboard';
$admin_active = 'dashboard';
require __DIR__ . '/includes/admin_header.php';
?>
<!-- Kartu statistik -->
<div class="row">
  <div class="col-md-4 col-sm-6">
    <div class="small-box bg-warning"><div class="inner"><h3><?= $total_booking ?></h3><p>Total Booking</p></div><div class="icon"><i class="fas fa-calendar-check"></i></div></div>
  </div>
  <div class="col-md-4 col-sm-6">
    <div class="small-box bg-info"><div class="inner"><h3><?= $booking_today ?></h3><p>Booking Hari Ini</p></div><div class="icon"><i class="fas fa-calendar-day"></i></div></div>
  </div>
  <div class="col-md-4 col-sm-6">
    <div class="small-box bg-secondary"><div class="inner"><h3><?= $booking_pending ?></h3><p>Booking Pending</p></div><div class="icon"><i class="fas fa-hourglass-half"></i></div></div>
  </div>
  <div class="col-md-4 col-sm-6">
    <div class="small-box bg-success"><div class="inner"><h3><?= $booking_done ?></h3><p>Booking Selesai</p></div><div class="icon"><i class="fas fa-check-double"></i></div></div>
  </div>
  <div class="col-md-4 col-sm-6">
    <div class="small-box bg-primary"><div class="inner"><h3><?= $total_testi ?></h3><p>Total Testimoni</p></div><div class="icon"><i class="fas fa-comment-dots"></i></div></div>
  </div>
  <div class="col-md-4 col-sm-6">
    <div class="small-box bg-danger"><div class="inner"><h3><?= $total_gallery ?></h3><p>Total Galeri</p></div><div class="icon"><i class="fas fa-images"></i></div></div>
  </div>
</div>

<!-- Grafik booking 12 bulan terakhir -->
<div class="card">
  <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Booking 12 Bulan Terakhir</h3></div>
  <div class="card-body"><canvas id="bookingChart" height="90"></canvas></div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
<script>
new Chart(document.getElementById('bookingChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($labels) ?>,
    datasets: [{
      label: 'Jumlah Booking',
      data: <?= json_encode($values) ?>,
      backgroundColor: 'rgba(217,142,50,.65)',
      borderColor: '#D98E32',
      borderWidth: 1
    }]
  },
  options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
