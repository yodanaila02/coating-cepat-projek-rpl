<?php
/**
 * =====================================================
 * FOOTER HALAMAN USER + FLOATING WHATSAPP BUTTON
 * Kompatibel InfinityFree
 * =====================================================
 */
$site_name = setting($db, 'site_name', 'Coating Cepat');
$wa        = setting($db, 'whatsapp', '081279788675');
$ig        = setting($db, 'instagram', '');
?>
<footer class="site-footer pt-5 pb-4 mt-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <h5 class="heading-font text-uppercase text-accent mb-3"><?= e($site_name) ?></h5>
        <p class="small text-secondary-light">Spesialis nano ceramic coating &amp; detailing mobil premium di Surakarta. Proteksi premium, kilap showroom yang tahan lama.</p>
        <div class="d-flex gap-3 fs-5">
          <a href="<?= e(wa_link($wa)) ?>" target="_blank" rel="noopener" class="text-accent" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
          <?php if ($ig): ?><a href="https://instagram.com/<?= e(ltrim($ig, '@')) ?>" target="_blank" rel="noopener" class="text-accent" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a><?php endif; ?>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <h6 class="heading-font text-uppercase mb-3">Menu</h6>
        <ul class="list-unstyled small footer-links">
          <li><a href="<?= e(BASE_URL) ?>/index.php?page=services">Layanan</a></li>
          <li><a href="<?= e(BASE_URL) ?>/index.php?page=gallery">Galeri</a></li>
          <li><a href="<?= e(BASE_URL) ?>/index.php?page=before-after">Before-After</a></li>
          <li><a href="<?= e(BASE_URL) ?>/index.php?page=calculator">Kalkulator Harga</a></li>
          <li><a href="<?= e(BASE_URL) ?>/index.php?page=booking">Booking Online</a></li>
          <li><a href="<?= e(BASE_URL) ?>/index.php?page=tracking">Tracking Booking</a></li>
        </ul>
      </div>
      <div class="col-md-2 col-6">
        <h6 class="heading-font text-uppercase mb-3">Bantuan</h6>
        <ul class="list-unstyled small footer-links">
          <li><a href="<?= e(BASE_URL) ?>/index.php?page=faq">FAQ</a></li>
          <li><a href="<?= e(BASE_URL) ?>/index.php?page=about">Tentang Kami</a></li>
          <li><a href="<?= e(BASE_URL) ?>/index.php?page=contact">Kontak</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h6 class="heading-font text-uppercase mb-3">Kontak</h6>
        <ul class="list-unstyled small text-secondary-light">
          <li class="mb-2"><i class="fa-solid fa-location-dot text-accent me-2"></i><?= e(setting($db, 'address')) ?></li>
          <li class="mb-2"><i class="fa-brands fa-whatsapp text-accent me-2"></i><?= e($wa) ?></li>
          <li><i class="fa-regular fa-clock text-accent me-2"></i><?= e(setting($db, 'open_hours')) ?></li>
        </ul>
      </div>
    </div>
    <hr class="border-secondary mt-4">
    <p class="text-center small text-secondary-light mb-0">&copy; <?= date('Y') ?> <?= e($site_name) ?>. All rights reserved.</p>
  </div>
</footer>

<!-- FLOATING WHATSAPP BUTTON -->
<a href="<?= e(wa_link($wa, 'Halo ' . $site_name . ', saya ingin bertanya tentang layanan coating/detailing.')) ?>"
   class="wa-float" target="_blank" rel="noopener" aria-label="Chat WhatsApp">
  <i class="fa-brands fa-whatsapp"></i>
</a>

<!-- SCRIPT CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<!-- Path JS pakai BASE_URL agar benar di root maupun subfolder -->
<script src="<?= e(BASE_URL) ?>/assets/js/main.js"></script>
</body>
</html>
