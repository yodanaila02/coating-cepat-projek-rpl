<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.10 — TRACKING BOOKING
 * Input kode booking -> tampilkan status, jadwal, layanan,
 * estimasi selesai, dan timeline status.
 * =====================================================
 */
$page_title = 'Tracking Booking';
$meta_desc  = 'Lacak status booking Coating Cepat Anda dengan memasukkan kode booking.';
$code = strtoupper(clean($_GET['code'] ?? ''));
$bk = null;
$not_found = false;

if ($code !== '') {
    $st = $db->prepare('SELECT b.*, s.name AS service_name, s.duration
                        FROM bookings b JOIN services s ON s.id = b.service_id
                        WHERE b.booking_code = ?');
    $st->execute([$code]);
    $bk = $st->fetch();
    if (!$bk) { $not_found = true; }
}
require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container" style="max-width:720px">
    <span class="title-line"></span>
    <h1 class="section-title">Tracking <span class="text-accent">Booking</span></h1>
    <p class="section-sub mb-4">Masukkan kode booking Anda (format: CC-XXXXXXXX-XXXX).</p>

    <form method="get" class="d-flex gap-2 mb-4">
      <input type="hidden" name="page" value="tracking">
      <input type="text" class="form-control form-control-lg text-uppercase" name="code"
             value="<?= e($code) ?>" placeholder="CC-20260607-AB12" required>
      <button class="btn btn-accent text-uppercase px-4" type="submit">Lacak</button>
    </form>

    <?php if ($not_found): ?>
      <div class="alert alert-danger">Kode booking <strong><?= e($code) ?></strong> tidak ditemukan. Periksa kembali penulisan kode.</div>
    <?php elseif ($bk): ?>
      <div class="cc-card p-4" data-aos="fade-up">
        <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center mb-3">
          <h5 class="text-uppercase mb-0"><?= e($bk['booking_code']) ?></h5>
          <?= status_badge($bk['status']) ?>
        </div>
        <div class="row small g-3 mb-4">
          <div class="col-6"><span class="text-secondary-light d-block">Nama</span><?= e($bk['name']) ?></div>
          <div class="col-6"><span class="text-secondary-light d-block">Layanan</span><?= e($bk['service_name']) ?></div>
          <div class="col-6"><span class="text-secondary-light d-block">Mobil</span><?= e($bk['car_brand'] . ' ' . $bk['car_type'] . ' ' . $bk['car_year'] . ' (' . $bk['car_color'] . ')') ?></div>
          <div class="col-6"><span class="text-secondary-light d-block">Jadwal</span><?= e(date('d-m-Y', strtotime($bk['booking_date'])) . ' • ' . $bk['booking_time']) ?> WIB</div>
          <div class="col-6"><span class="text-secondary-light d-block">Estimasi Selesai</span><?= e($bk['duration']) ?> sejak pengerjaan dimulai</div>
          <?php if ($bk['notes']): ?>
            <div class="col-6"><span class="text-secondary-light d-block">Catatan</span><?= e($bk['notes']) ?></div>
          <?php endif; ?>
        </div>

        <!-- Timeline status -->
        <h6 class="text-uppercase text-accent">Timeline Status</h6>
        <?php
        /* Urutan timeline normal; rejected ditangani terpisah */
        $flow = [
            'pending'     => 'Booking diterima — menunggu konfirmasi admin',
            'confirmed'   => 'Booking dikonfirmasi — jadwal Anda sudah terkunci',
            'rescheduled' => 'Jadwal diubah (cek jadwal terbaru di atas)',
            'done'        => 'Pengerjaan selesai — mobil siap diambil',
        ];
        $order = array_keys($flow);
        $current = $bk['status'];
        $current_idx = array_search($current, $order, true);
        ?>
        <?php if ($current === 'rejected'): ?>
          <ul class="timeline mt-3">
            <li class="done"><strong>Booking diterima</strong><br><small class="text-secondary-light">Booking masuk ke sistem kami</small></li>
            <li class="current"><strong>Booking ditolak</strong><br><small class="text-secondary-light">Mohon maaf, booking tidak dapat diproses. Silakan hubungi kami via WhatsApp untuk info lebih lanjut atau buat booking baru.</small></li>
          </ul>
        <?php else: ?>
          <ul class="timeline mt-3">
            <?php foreach ($flow as $i_status => $desc):
              $idx = array_search($i_status, $order, true);
              if ($i_status === 'rescheduled' && $current !== 'rescheduled') continue; // tampilkan reschedule hanya jika terjadi
              $cls = '';
              if ($idx < $current_idx) $cls = 'done';
              if ($i_status === $current) $cls = 'current';
            ?>
            <li class="<?= $cls ?>">
              <strong><?= e(ucfirst($i_status === 'pending' ? 'Menunggu Konfirmasi' : ($i_status === 'confirmed' ? 'Dikonfirmasi' : ($i_status === 'rescheduled' ? 'Dijadwal Ulang' : 'Selesai')))) ?></strong><br>
              <small class="text-secondary-light"><?= e($desc) ?></small>
            </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <div class="small text-secondary-light">Terakhir diperbarui: <?= e(date('d-m-Y H:i', strtotime($bk['updated_at']))) ?> WIB</div>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
