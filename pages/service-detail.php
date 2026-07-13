<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.4 — DETAIL LAYANAN
 * Nama, deskripsi lengkap, benefit (list), harga, FAQ terkait,
 * galeri terkait layanan, tombol Booking (layanan ter-preselect).
 * =====================================================
 */
$id = (int)($_GET['id'] ?? 0);
$st = $db->prepare('SELECT * FROM services WHERE id = ?');
$st->execute([$id]);
$service = $st->fetch();

if (!$service) { redirect('index.php?page=services'); }

$page_title = $service['name'];
$meta_desc  = mb_strimwidth(strip_tags($service['description']), 0, 155, '...');

/* Galeri terkait layanan (berdasarkan kategori) */
$gst = $db->prepare('SELECT * FROM gallery WHERE category = ? ORDER BY created_at DESC LIMIT 6');
$gst->execute([$service['category']]);
$related_gallery = $gst->fetchAll();

/* FAQ terkait (Coating -> kategori Ceramic Coating, lainnya -> Detailing) */
$faq_cat = $service['category'] === 'coating' ? 'Ceramic Coating' : 'Detailing';
$fst = $db->prepare('SELECT * FROM faqs WHERE category = ? ORDER BY id LIMIT 4');
$fst->execute([$faq_cat]);
$related_faqs = $fst->fetchAll();

$benefits = array_filter(array_map('trim', explode("\n", (string)$service['benefits'])));
require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="index.php?page=home">Beranda</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=services">Layanan</a></li>
        <li class="breadcrumb-item active text-secondary-light"><?= e($service['name']) ?></li>
      </ol>
    </nav>

    <div class="row g-5">
      <div class="col-lg-7" data-aos="fade-up">
        <span class="title-line"></span>
        <h1 class="section-title"><?= e($service['name']) ?></h1>
        <img src="<?= img_url($service['thumbnail'], $service['name']) ?>" alt="<?= e($service['name']) ?>" class="img-fluid border my-4" style="border-color:var(--cc-gray)!important">
        <p class="text-secondary-light"><?= nl2br(e($service['description'])) ?></p>

        <?php if ($benefits): ?>
        <h4 class="text-uppercase mt-4">Benefit</h4>
        <ul class="list-unstyled">
          <?php foreach ($benefits as $b): ?>
            <li class="mb-2"><i class="fa-solid fa-check text-accent me-2"></i><?= e($b) ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <div class="col-lg-5">
        <!-- Kartu harga + tombol booking (layanan ter-preselect via ?service=ID) -->
        <div class="cc-card p-4 mb-4" data-aos="fade-up">
          <h5 class="text-uppercase">Harga Mulai</h5>
          <div class="price-tag fs-3 mb-2"><?= format_rupiah($service['price']) ?></div>
          <p class="small text-secondary-light mb-1"><i class="fa-regular fa-clock me-2"></i>Estimasi pengerjaan: <?= e($service['duration']) ?></p>
          <p class="small text-secondary-light">Harga final menyesuaikan ukuran &amp; kondisi mobil. Cek <a href="index.php?page=calculator">kalkulator estimasi</a>.</p>
          <a href="index.php?page=booking&service=<?= (int)$service['id'] ?>" class="btn btn-accent w-100 text-uppercase">Booking Layanan Ini</a>
        </div>

        <?php if ($related_faqs): ?>
        <!-- FAQ terkait layanan -->
        <div data-aos="fade-up">
          <h5 class="text-uppercase mb-3">FAQ Terkait</h5>
          <div class="accordion" id="faqService">
            <?php foreach ($related_faqs as $i => $f): ?>
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed small" type="button" data-bs-toggle="collapse" data-bs-target="#fq<?= $i ?>">
                  <?= e($f['question']) ?>
                </button>
              </h2>
              <div id="fq<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faqService">
                <div class="accordion-body small"><?= e($f['answer']) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($related_gallery): ?>
    <!-- Galeri terkait layanan -->
    <div class="mt-5">
      <h4 class="text-uppercase mb-4">Galeri <?= e($service['name']) ?></h4>
      <div class="row g-3">
        <?php foreach ($related_gallery as $g): ?>
        <div class="col-6 col-md-4 col-lg-2">
          <a href="<?= img_url($g['image'], $g['title']) ?>" data-lightbox="service-gallery" data-title="<?= e($g['title']) ?>">
            <div class="cc-card"><img src="<?= img_url($g['image'], $g['title']) ?>" alt="<?= e($g['title']) ?>" loading="lazy"></div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
