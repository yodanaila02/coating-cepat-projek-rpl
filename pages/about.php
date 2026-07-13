<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.2 — TENTANG KAMI
 * Profil usaha, pengalaman, sertifikasi, teknologi coating,
 * dan foto workshop.
 * =====================================================
 */
$page_title = 'Tentang Kami';
$meta_desc  = 'Profil Coating Cepat: spesialis nano ceramic coating dan detailing mobil premium di Surakarta dengan teknisi bersertifikat dan teknologi coating terkini.';
$site_name  = setting($db, 'site_name', 'Coating Cepat');
require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <span class="title-line"></span>
    <h1 class="section-title">Tentang <span class="text-accent"><?= e($site_name) ?></span></h1>
    <p class="section-sub mb-5">Detailing bukan sekadar membuat mobil bersih — tapi seni merawat dan melindungi.</p>

    <div class="row g-5 align-items-center">
      <div class="col-lg-6" data-aos="fade-right">
        <!-- Foto workshop -->
        <img src="<?= img_url(null, 'Workshop ' . $site_name) ?>" alt="Foto workshop <?= e($site_name) ?>" class="img-fluid border" style="border-color:var(--cc-gray)!important">
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <h3 class="text-uppercase">Profil Usaha</h3>
        <p class="text-secondary-light"><?= e($site_name) ?> adalah workshop detailing dan ceramic coating yang berlokasi di <?= e(setting($db, 'address')) ?>. Berdiri sejak <?= date('Y') - (int)setting($db, 'stat_years', '5') ?>, kami telah menangani lebih dari <?= e(setting($db, 'stat_cars', '350')) ?> unit mobil — dari city car harian hingga SUV premium.</p>
        <p class="text-secondary-light">Setiap mobil dikerjakan mengikuti SOP per panel: inspeksi awal dengan lighting khusus, pengukuran ketebalan cat, dekontaminasi, koreksi bertahap, hingga aplikasi coating di ruang semi-steril.</p>
      </div>
    </div>

    <div class="row g-4 mt-4">
      <div class="col-md-4" data-aos="fade-up">
        <div class="why-card">
          <i class="fa-solid fa-certificate mb-3 d-block"></i>
          <h5 class="text-uppercase">Sertifikasi</h5>
          <p class="small text-secondary-light mb-0">Teknisi kami tersertifikasi aplikator coating profesional dan rutin mengikuti pelatihan produk detailing terbaru.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="why-card">
          <i class="fa-solid fa-flask mb-3 d-block"></i>
          <h5 class="text-uppercase">Teknologi Coating</h5>
          <p class="small text-secondary-light mb-0">Menggunakan nano ceramic coating SiO2 9H grade profesional, polisher dual-action, paint thickness gauge, dan steam cleaner industri.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="why-card">
          <i class="fa-solid fa-award mb-3 d-block"></i>
          <h5 class="text-uppercase">Pengalaman</h5>
          <p class="small text-secondary-light mb-0"><?= e(setting($db, 'stat_years', '5')) ?> tahun melayani pelanggan Surakarta &amp; sekitarnya dengan rating kepuasan <?= e(setting($db, 'stat_rating', '4.9')) ?>/5.</p>
        </div>
      </div>
    </div>

    <div class="text-center mt-5">
      <a href="index.php?page=booking" class="btn btn-accent text-uppercase px-4">Booking Sekarang</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
