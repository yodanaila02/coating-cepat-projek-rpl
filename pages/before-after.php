<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.6 — BEFORE-AFTER
 * Daftar perbandingan foto sebelum/sesudah menggunakan
 * slider comparison drag handle (JS Vanilla: input range
 * + clip-path, lihat assets/js/main.js).
 * =====================================================
 */
$page_title = 'Before - After';
$meta_desc  = 'Lihat perbandingan kondisi mobil sebelum dan sesudah dikerjakan Coating Cepat. Geser slider untuk melihat perbedaannya.';
$items = $db->query('SELECT ba.*, s.name AS service_name
                     FROM before_after ba
                     LEFT JOIN services s ON s.id = ba.service_id
                     ORDER BY ba.created_at DESC, ba.id DESC')->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <span class="title-line"></span>
    <h1 class="section-title">Before <span class="text-accent">/ After</span></h1>
    <p class="section-sub mb-5">Geser handle ke kiri-kanan untuk membandingkan hasil pengerjaan.</p>

    <div class="row g-4">
      <?php foreach ($items as $i => $item): ?>
      <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?= ($i % 2) * 100 ?>">
        <div class="ba-slider">
          <span class="ba-label before">Before</span>
          <span class="ba-label after">After</span>
          <img src="<?= img_url($item['before_image'], 'Before') ?>" alt="Sebelum - <?= e($item['title']) ?>">
          <img class="ba-after" src="<?= img_url($item['after_image'], 'After') ?>" alt="Sesudah - <?= e($item['title']) ?>">
          <div class="ba-handle"></div>
          <input type="range" min="0" max="100" value="50" aria-label="Geser perbandingan <?= e($item['title']) ?>">
        </div>
        <div class="mt-2">
          <strong class="text-uppercase small"><?= e($item['title']) ?></strong>
          <?php if ($item['service_name']): ?>
            <span class="badge bg-dark border ms-2" style="border-color:var(--cc-accent)!important;color:var(--cc-accent)"><?= e($item['service_name']) ?></span>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (!$items): ?><p class="text-secondary-light">Belum ada data before-after.</p><?php endif; ?>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
