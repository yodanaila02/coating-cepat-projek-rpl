<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.9 (lanjutan) — HALAMAN SUKSES BOOKING
 * Menampilkan kode booking unik + tombol "Konfirmasi via
 * WhatsApp" yang membuka wa.me dengan TEMPLATE PESAN
 * OTOMATIS berisi detail booking (reminder via WhatsApp).
 * =====================================================
 */
$code = clean($_GET['code'] ?? '');
$st = $db->prepare('SELECT b.*, s.name AS service_name, s.price
                    FROM bookings b JOIN services s ON s.id = b.service_id
                    WHERE b.booking_code = ?');
$st->execute([$code]);
$bk = $st->fetch();

if (!$bk) { redirect('index.php?page=booking'); }

$page_title = 'Booking Berhasil';
$site_name  = setting($db, 'site_name', 'Coating Cepat');
$wa         = setting($db, 'whatsapp', '081279788675');

/* TEMPLATE PESAN WHATSAPP OTOMATIS (konfirmasi & reminder booking) */
$wa_template = "Halo {$site_name}, saya ingin konfirmasi booking:\n"
             . "Kode Booking: {$bk['booking_code']}\n"
             . "Nama: {$bk['name']}\n"
             . "Mobil: {$bk['car_brand']} {$bk['car_type']} {$bk['car_year']} ({$bk['car_color']})\n"
             . "Layanan: {$bk['service_name']}\n"
             . "Jadwal: " . date('d-m-Y', strtotime($bk['booking_date'])) . " jam {$bk['booking_time']} WIB\n"
             . "Mohon dikonfirmasi. Terima kasih.";

require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container text-center" style="max-width:640px">
    <i class="fa-solid fa-circle-check text-accent" style="font-size:4rem"></i>
    <h1 class="section-title mt-3">Booking <span class="text-accent">Berhasil!</span></h1>
    <p class="text-secondary-light">Simpan kode booking Anda untuk tracking status pengerjaan.</p>

    <div class="cc-card p-4 my-4">
      <div class="small text-secondary-light text-uppercase">Kode Booking Anda</div>
      <div class="price-tag" style="font-size:2rem;letter-spacing:.1em"><?= e($bk['booking_code']) ?></div>
      <hr class="border-secondary">
      <div class="row text-start small g-2">
        <div class="col-6"><span class="text-secondary-light">Nama</span><br><?= e($bk['name']) ?></div>
        <div class="col-6"><span class="text-secondary-light">Layanan</span><br><?= e($bk['service_name']) ?></div>
        <div class="col-6"><span class="text-secondary-light">Mobil</span><br><?= e($bk['car_brand'] . ' ' . $bk['car_type'] . ' ' . $bk['car_year']) ?></div>
        <div class="col-6"><span class="text-secondary-light">Jadwal</span><br><?= e(date('d-m-Y', strtotime($bk['booking_date'])) . ' • ' . $bk['booking_time']) ?> WIB</div>
        <div class="col-12 mt-2"><span class="text-secondary-light">Status</span><br><?= status_badge($bk['status']) ?></div>
      </div>
    </div>

    <!-- Tombol konfirmasi via WhatsApp (template otomatis) -->
    <div class="d-flex flex-wrap justify-content-center gap-3">
      <a href="<?= e(wa_link($wa, $wa_template)) ?>" target="_blank" rel="noopener" class="btn btn-accent btn-lg text-uppercase">
        <i class="fa-brands fa-whatsapp me-2"></i>Konfirmasi via WhatsApp
      </a>
      <a href="index.php?page=tracking&code=<?= e(urlencode($bk['booking_code'])) ?>" class="btn btn-outline-accent btn-lg text-uppercase">Tracking Booking</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
