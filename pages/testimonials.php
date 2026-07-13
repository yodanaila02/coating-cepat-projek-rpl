<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.7 — TESTIMONI
 * Nama pelanggan, kendaraan, rating bintang, komentar,
 * dan foto kendaraan.
 * =====================================================
 */
$page_title = 'Testimoni';
$meta_desc  = 'Testimoni asli pelanggan Coating Cepat tentang hasil ceramic coating dan detailing mobil mereka.';
$items = $db->query('SELECT * FROM testimonials ORDER BY created_at DESC, id DESC')->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <span class="title-line"></span>
    <h1 class="section-title">Testimoni <span class="text-accent">Pelanggan</span></h1>
    <p class="section-sub mb-5">Apa kata mereka yang sudah mempercayakan mobilnya kepada kami.</p>

    <div class="row g-4">
      <?php foreach ($items as $i => $t): ?>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 100 ?>">
        <div class="testi-card">
          <?php if ($t['photo']): ?>
            <img src="<?= img_url($t['photo'], $t['vehicle']) ?>" alt="Kendaraan <?= e($t['name']) ?>" class="img-fluid mb-3 border" style="border-color:var(--cc-gray)!important;aspect-ratio:16/9;object-fit:cover;width:100%">
          <?php endif; ?>
          <div class="stars mb-2">
            <?php for ($s = 1; $s <= 5; $s++): ?>
              <i class="fa-<?= $s <= (int)$t['rating'] ? 'solid' : 'regular' ?> fa-star"></i>
            <?php endfor; ?>
          </div>
          <p class="small">"<?= e($t['comment']) ?>"</p>
          <strong class="d-block"><?= e($t['name']) ?></strong>
          <span class="small text-secondary-light"><?= e($t['vehicle']) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (!$items): ?><p class="text-secondary-light">Belum ada testimoni.</p><?php endif; ?>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
