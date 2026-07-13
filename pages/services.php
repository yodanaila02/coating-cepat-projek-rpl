<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.3 — DAFTAR LAYANAN (dari database)
 * Menampilkan deskripsi, benefit, estimasi pengerjaan,
 * dan harga mulai untuk tiap layanan.
 * =====================================================
 */
$page_title = 'Layanan';
$meta_desc  = 'Layanan Coating Cepat: Nano Ceramic Coating, Paint Correction, Interior Detailing, Headlamp Restoration, dan Engine Detailing dengan harga transparan.';
$services   = $db->query('SELECT * FROM services ORDER BY id')->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <span class="title-line"></span>
    <h1 class="section-title">Layanan <span class="text-accent">Kami</span></h1>
    <p class="section-sub mb-5">Pilih perawatan yang mobil Anda butuhkan — semua dikerjakan dengan standar premium.</p>

    <div class="row g-4">
      <?php foreach ($services as $i => $s):
        $benefits = array_filter(array_map('trim', explode("\n", (string)$s['benefits'])));
      ?>
      <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?= ($i % 2) * 100 ?>">
        <div class="cc-card">
          <img src="<?= img_url($s['thumbnail'], $s['name']) ?>" alt="<?= e($s['name']) ?>" loading="lazy">
          <div class="p-4">
            <h3 class="text-uppercase h5"><?= e($s['name']) ?></h3>
            <p class="small text-secondary-light"><?= e(mb_strimwidth($s['description'], 0, 180, '...')) ?></p>
            <?php if ($benefits): ?>
            <ul class="small text-secondary-light ps-3 mb-3">
              <?php foreach (array_slice($benefits, 0, 3) as $b): ?>
                <li><?= e($b) ?></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
              <div>
                <div class="price-tag">Mulai <?= format_rupiah($s['price']) ?></div>
                <small class="text-secondary-light"><i class="fa-regular fa-clock me-1"></i>Estimasi: <?= e($s['duration']) ?></small>
              </div>
              <div class="d-flex gap-2">
                <a href="index.php?page=service-detail&id=<?= (int)$s['id'] ?>" class="btn btn-outline-accent btn-sm">Detail</a>
                <a href="index.php?page=booking&service=<?= (int)$s['id'] ?>" class="btn btn-accent btn-sm">Booking</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
