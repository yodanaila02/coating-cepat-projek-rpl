<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.1 — HOMEPAGE (index halaman user)
 * Berisi: hero banner, 2 CTA, Why Choose Us (4 kartu),
 * counter statistik, layanan unggulan, before-after slider,
 * galeri terbaru (6 foto), testimoni Swiper, CTA penutup.
 * =====================================================
 */
$page_title = ''; // pakai meta_title default dari pengaturan
$meta_desc  = setting($db, 'meta_description');
$site_name  = setting($db, 'site_name', 'Coating Cepat');
$wa         = setting($db, 'whatsapp', '081279788675');

/* Data dari database */
$services     = $db->query('SELECT * FROM services WHERE is_featured = 1 ORDER BY id LIMIT 5')->fetchAll();
$gallery      = $db->query('SELECT * FROM gallery ORDER BY created_at DESC, id DESC LIMIT 6')->fetchAll();
$testimonials = $db->query('SELECT * FROM testimonials ORDER BY created_at DESC, id DESC LIMIT 8')->fetchAll();
$ba           = $db->query('SELECT * FROM before_after ORDER BY created_at DESC, id DESC LIMIT 1')->fetch();

require __DIR__ . '/../includes/header.php';
?>

<!-- ================= HERO BANNER ================= -->
<header class="hero">
  <div class="container py-5">
    <div class="row">
      <div class="col-lg-9">
        <p class="kicker mb-3" data-aos="fade-up">Premium Auto Detailing &mdash; Surakarta</p>
        <h1 data-aos="fade-up" data-aos-delay="100">
          Proteksi Premium,<br>
          <span class="accent">Kilap Showroom</span> yang Tahan Lama
        </h1>
        <p class="lead text-secondary-light mt-3 mb-4" style="max-width:560px" data-aos="fade-up" data-aos-delay="200">
          Nano ceramic coating, paint correction, dan detailing menyeluruh oleh teknisi berpengalaman. Mobil Anda layak mendapat perlindungan terbaik.
        </p>
        <!-- CTA: Booking Sekarang (solid oranye) + Chat WhatsApp (outline) -->
        <div class="d-flex flex-wrap gap-3" data-aos="fade-up" data-aos-delay="300">
          <a href="index.php?page=booking" class="btn btn-accent btn-lg px-4 text-uppercase">Booking Sekarang</a>
          <a href="<?= e(wa_link($wa, 'Halo ' . $site_name . ', saya ingin konsultasi layanan coating.')) ?>"
             target="_blank" rel="noopener" class="btn btn-outline-accent btn-lg px-4 text-uppercase">
            <i class="fa-brands fa-whatsapp me-2"></i>Chat WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- ================= WHY CHOOSE US (4 kartu bernomor 01-04) ================= -->
<section class="section">
  <div class="container">
    <span class="title-line"></span>
    <h2 class="section-title">Kenapa Memilih <span class="text-accent"><?= e($site_name) ?></span></h2>
    <p class="section-sub mb-5">Empat alasan mobil Anda berada di tangan yang tepat.</p>
    <div class="row g-4">
      <?php
      $reasons = [
        ['01', 'fa-shield-halved',  'Proteksi 9H Bersertifikat', 'Lapisan nano ceramic premium dengan kekerasan 9H, melindungi cat dari UV, jamur, dan baret halus hingga 5 tahun.'],
        ['02', 'fa-user-gear',      'Teknisi Berpengalaman', 'Dikerjakan teknisi terlatih dengan SOP per panel, paint thickness gauge, dan lighting inspeksi khusus.'],
        ['03', 'fa-droplet',        'Efek Hydrophobic Maksimal', 'Air dan kotoran langsung mengalir dari permukaan cat — mobil lebih mudah dicuci dan tetap kinclong.'],
        ['04', 'fa-clock-rotate-left', 'Tepat Waktu &amp; Bergaransi', 'Estimasi pengerjaan jelas sejak awal, hasil dijamin, dan setiap aplikasi coating disertai garansi resmi.'],
      ];
      foreach ($reasons as $i => $r): ?>
      <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
        <div class="why-card">
          <div class="num mb-3"><?= $r[0] ?></div>
          <i class="fa-solid <?= $r[1] ?> mb-3 d-block"></i>
          <h5 class="text-uppercase"><?= $r[2] ?></h5>
          <p class="small text-secondary-light mb-0"><?= $r[3] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ================= STATISTIK PENGERJAAN (counter animasi) ================= -->
<section class="py-5" style="background:var(--cc-dark);border-top:1px solid var(--cc-gray);border-bottom:1px solid var(--cc-gray)">
  <div class="container">
    <div class="row g-4 text-start">
      <div class="col-6 col-lg-3" data-aos="fade-up">
        <div class="stat-box">
          <div class="value"><span data-counter="<?= e(setting($db, 'stat_cars', '350')) ?>">0</span>+</div>
          <div class="small text-secondary-light text-uppercase">Mobil Dikerjakan</div>
        </div>
      </div>
      <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-box">
          <div class="value"><span data-counter="<?= e(setting($db, 'stat_years', '5')) ?>">0</span></div>
          <div class="small text-secondary-light text-uppercase">Tahun Pengalaman</div>
        </div>
      </div>
      <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-box">
          <div class="value"><span data-counter="<?= e(setting($db, 'stat_rating', '4.9')) ?>">0</span>/5</div>
          <div class="small text-secondary-light text-uppercase">Rating Pelanggan</div>
        </div>
      </div>
      <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
        <div class="stat-box">
          <div class="value"><span data-counter="<?= e(setting($db, 'stat_customers', '300')) ?>">0</span>+</div>
          <div class="small text-secondary-light text-uppercase">Pelanggan Puas</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= LAYANAN UNGGULAN (dari database) ================= -->
<section class="section">
  <div class="container">
    <span class="title-line"></span>
    <h2 class="section-title">Layanan <span class="text-accent">Unggulan</span></h2>
    <p class="section-sub mb-5">Perawatan menyeluruh, dari eksterior hingga ruang mesin.</p>
    <div class="row g-4">
      <?php foreach ($services as $i => $s): ?>
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 100 ?>">
        <div class="cc-card">
          <img src="<?= img_url($s['thumbnail'], $s['name']) ?>" alt="<?= e($s['name']) ?>" loading="lazy">
          <div class="p-4">
            <h5 class="text-uppercase mb-2"><?= e($s['name']) ?></h5>
            <p class="small text-secondary-light"><?= e(mb_strimwidth($s['description'], 0, 130, '...')) ?></p>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <span class="price-tag">Mulai <?= format_rupiah($s['price']) ?></span>
              <a href="index.php?page=service-detail&id=<?= (int)$s['id'] ?>" class="btn btn-outline-accent btn-sm">Detail</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="index.php?page=services" class="btn btn-accent text-uppercase px-4">Lihat Semua Layanan</a>
    </div>
  </div>
</section>

<!-- ================= BEFORE-AFTER SLIDER COMPARISON (drag handle) ================= -->
<?php if ($ba): ?>
<section class="section pt-0">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5" data-aos="fade-right">
        <span class="title-line"></span>
        <h2 class="section-title">Bukti Nyata <span class="text-accent">Before / After</span></h2>
        <p class="text-secondary-light">Geser handle di tengah foto untuk melihat perbedaan sebelum dan sesudah pengerjaan kami. Hasil berbicara lebih keras daripada janji.</p>
        <a href="index.php?page=before-after" class="btn btn-outline-accent text-uppercase mt-2">Lihat Semua Perbandingan</a>
      </div>
      <div class="col-lg-7" data-aos="fade-left">
        <div class="ba-slider">
          <span class="ba-label before">Before</span>
          <span class="ba-label after">After</span>
          <img src="<?= img_url($ba['before_image'], 'Before') ?>" alt="Sebelum - <?= e($ba['title']) ?>">
          <img class="ba-after" src="<?= img_url($ba['after_image'], 'After') ?>" alt="Sesudah - <?= e($ba['title']) ?>">
          <div class="ba-handle"></div>
          <input type="range" min="0" max="100" value="50" aria-label="Geser perbandingan before-after">
        </div>
        <p class="small text-secondary-light mt-2 mb-0"><?= e($ba['title']) ?></p>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ================= GALERI TERBARU (6 foto terakhir) ================= -->
<section class="section pt-0">
  <div class="container">
    <span class="title-line"></span>
    <h2 class="section-title">Galeri <span class="text-accent">Terbaru</span></h2>
    <p class="section-sub mb-5">Dokumentasi pengerjaan terbaru di workshop kami.</p>
    <div class="row g-3">
      <?php foreach ($gallery as $i => $g): ?>
      <div class="col-6 col-md-4" data-aos="zoom-in" data-aos-delay="<?= ($i % 3) * 80 ?>">
        <a href="<?= img_url($g['image'], $g['title']) ?>" data-lightbox="home-gallery" data-title="<?= e($g['title']) ?>">
          <div class="cc-card">
            <img src="<?= img_url($g['image'], $g['title']) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="index.php?page=gallery" class="btn btn-outline-accent text-uppercase px-4">Buka Galeri Lengkap</a>
    </div>
  </div>
</section>

<!-- ================= TESTIMONI PELANGGAN (SwiperJS carousel) ================= -->
<?php if ($testimonials): ?>
<section class="section pt-0">
  <div class="container">
    <span class="title-line"></span>
    <h2 class="section-title">Kata <span class="text-accent">Pelanggan</span></h2>
    <p class="section-sub mb-5">Kepuasan pelanggan adalah portofolio terbaik kami.</p>
    <div class="swiper testi-swiper" data-aos="fade-up">
      <div class="swiper-wrapper pb-5">
        <?php foreach ($testimonials as $t): ?>
        <div class="swiper-slide h-auto">
          <div class="testi-card">
            <div class="stars mb-2">
              <?php for ($s = 1; $s <= 5; $s++): ?>
                <i class="fa-<?= $s <= (int)$t['rating'] ? 'solid' : 'regular' ?> fa-star"></i>
              <?php endfor; ?>
            </div>
            <p class="small">"<?= e($t['comment']) ?>"</p>
            <div class="mt-3">
              <strong class="d-block"><?= e($t['name']) ?></strong>
              <span class="small text-secondary-light"><?= e($t['vehicle']) ?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ================= CTA PENUTUP ================= -->
<section class="cta-band py-5">
  <div class="container text-center py-4">
    <h2 class="section-title mb-3" data-aos="fade-up">Siap Bikin Mobil Anda <span class="text-accent">Kinclong Maksimal?</span></h2>
    <p class="section-sub mx-auto mb-4" data-aos="fade-up" data-aos-delay="100">Slot pengerjaan terbatas setiap hari. Amankan jadwal Anda sekarang.</p>
    <div class="d-flex justify-content-center flex-wrap gap-3" data-aos="fade-up" data-aos-delay="200">
      <a href="index.php?page=booking" class="btn btn-accent btn-lg text-uppercase px-4">Booking Sekarang</a>
      <a href="<?= e(wa_link($wa, 'Halo ' . $site_name . ', saya ingin booking layanan.')) ?>" target="_blank" rel="noopener" class="btn btn-outline-accent btn-lg text-uppercase px-4">
        <i class="fa-brands fa-whatsapp me-2"></i>Chat WhatsApp
      </a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
