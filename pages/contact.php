<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.12 — KONTAK
 * Alamat, Google Maps embed (iframe dari pengaturan),
 * nomor WA, Instagram, jam operasional.
 * =====================================================
 */
$page_title = 'Kontak';
$meta_desc  = 'Hubungi Coating Cepat: alamat workshop di Fajar, Surakarta, nomor WhatsApp, Instagram, dan jam operasional.';
$site_name  = setting($db, 'site_name', 'Coating Cepat');
$wa         = setting($db, 'whatsapp', '081279788675');
$ig         = setting($db, 'instagram', '');
require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <span class="title-line"></span>
    <h1 class="section-title">Hubungi <span class="text-accent">Kami</span></h1>
    <p class="section-sub mb-5">Workshop kami terbuka untuk konsultasi gratis — datang langsung atau chat dulu.</p>

    <div class="row g-4">
      <div class="col-lg-5" data-aos="fade-right">
        <div class="cc-card p-4 h-100">
          <ul class="list-unstyled mb-0">
            <li class="mb-4">
              <i class="fa-solid fa-location-dot text-accent me-2"></i>
              <strong class="text-uppercase">Alamat</strong>
              <p class="small text-secondary-light mb-0 mt-1"><?= e(setting($db, 'address')) ?></p>
            </li>
            <li class="mb-4">
              <i class="fa-brands fa-whatsapp text-accent me-2"></i>
              <strong class="text-uppercase">WhatsApp</strong>
              <p class="small mb-0 mt-1">
                <a href="<?= e(wa_link($wa, 'Halo ' . $site_name . ', saya ingin bertanya.')) ?>" target="_blank" rel="noopener"><?= e($wa) ?></a>
              </p>
            </li>
            <?php if ($ig): ?>
            <li class="mb-4">
              <i class="fa-brands fa-instagram text-accent me-2"></i>
              <strong class="text-uppercase">Instagram</strong>
              <p class="small mb-0 mt-1">
                <a href="https://instagram.com/<?= e(ltrim($ig, '@')) ?>" target="_blank" rel="noopener">@<?= e(ltrim($ig, '@')) ?></a>
              </p>
            </li>
            <?php endif; ?>
            <li>
              <i class="fa-regular fa-clock text-accent me-2"></i>
              <strong class="text-uppercase">Jam Operasional</strong>
              <p class="small text-secondary-light mb-0 mt-1"><?= e(setting($db, 'open_hours')) ?></p>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-lg-7" data-aos="fade-left">
        <!-- Google Maps embed (URL diatur dari admin > Pengaturan) -->
        <div class="border" style="border-color:var(--cc-gray)!important">
          <iframe src="<?= e(setting($db, 'maps_url', 'https://www.google.com/maps?q=Fajar,+Surakarta,+Jawa+Tengah&output=embed')) ?>"
                  width="100%" height="420" style="border:0;display:block" loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade" title="Lokasi <?= e($site_name) ?>"></iframe>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
