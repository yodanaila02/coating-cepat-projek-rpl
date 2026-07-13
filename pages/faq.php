<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.11 — FAQ
 * Accordion Bootstrap dikelompokkan per kategori:
 * Ceramic Coating, Detailing, Booking.
 * =====================================================
 */
$page_title = 'FAQ';
$meta_desc  = 'Pertanyaan yang sering diajukan seputar ceramic coating, detailing, dan cara booking di Coating Cepat.';
$faqs = $db->query("SELECT * FROM faqs ORDER BY FIELD(category,'Ceramic Coating','Detailing','Booking'), id")->fetchAll();
$grouped = [];
foreach ($faqs as $f) { $grouped[$f['category']][] = $f; }
require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container" style="max-width:860px">
    <span class="title-line"></span>
    <h1 class="section-title">Frequently Asked <span class="text-accent">Questions</span></h1>
    <p class="section-sub mb-5">Tidak menemukan jawaban? Hubungi kami via WhatsApp.</p>

    <?php $n = 0; foreach ($grouped as $cat => $items): ?>
      <h4 class="text-uppercase mt-4 mb-3 text-accent"><?= e($cat) ?></h4>
      <div class="accordion mb-4" id="faqAcc<?= md5($cat) ?>">
        <?php foreach ($items as $f): $n++; ?>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $n ?>">
              <?= e($f['question']) ?>
            </button>
          </h2>
          <div id="faq<?= $n ?>" class="accordion-collapse collapse" data-bs-parent="#faqAcc<?= md5($cat) ?>">
            <div class="accordion-body"><?= nl2br(e($f['answer'])) ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <?php if (!$faqs): ?><p class="text-secondary-light">Belum ada FAQ.</p><?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
