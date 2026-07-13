<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.5 — GALERI
 * Filter kategori (JS Vanilla): Semua, Coating, Paint
 * Correction, Interior, Headlamp, Engine.
 * Grid foto + judul + deskripsi + preview Lightbox2.
 * =====================================================
 */
$page_title = 'Galeri';
$meta_desc  = 'Galeri hasil pengerjaan Coating Cepat: ceramic coating, paint correction, interior detailing, headlamp restoration, dan engine detailing.';
$gallery    = $db->query('SELECT * FROM gallery ORDER BY created_at DESC, id DESC')->fetchAll();
$cats = [
  'all' => 'Semua', 'coating' => 'Coating', 'paint-correction' => 'Paint Correction',
  'interior' => 'Interior', 'headlamp' => 'Headlamp', 'engine' => 'Engine',
];
require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <span class="title-line"></span>
    <h1 class="section-title">Galeri <span class="text-accent">Pengerjaan</span></h1>
    <p class="section-sub mb-4">Dokumentasi asli dari workshop kami — tanpa filter, tanpa rekayasa.</p>

    <!-- Tombol filter kategori (JS Vanilla, lihat assets/js/main.js) -->
    <div class="d-flex flex-wrap gap-2 mb-4">
      <?php foreach ($cats as $key => $label): ?>
        <button type="button" class="filter-btn <?= $key === 'all' ? 'active' : '' ?>" data-filter="<?= $key ?>"><?= e($label) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="row g-3">
      <?php foreach ($gallery as $g): ?>
      <div class="col-6 col-md-4 col-lg-3 gallery-item" data-category="<?= e($g['category']) ?>">
        <a href="<?= img_url($g['image'], $g['title']) ?>" data-lightbox="gallery" data-title="<?= e($g['title']) ?> — <?= e($g['description']) ?>">
          <div class="cc-card">
            <img src="<?= img_url($g['image'], $g['title']) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
            <div class="p-3">
              <strong class="d-block small text-uppercase"><?= e($g['title']) ?></strong>
              <span class="small text-secondary-light"><?= e($g['description']) ?></span>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
      <?php if (!$gallery): ?>
        <p class="text-secondary-light">Belum ada foto galeri. Silakan tambahkan via admin panel.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
