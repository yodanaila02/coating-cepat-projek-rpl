<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.8 — KALKULATOR ESTIMASI HARGA
 * User memilih: jenis mobil, ukuran (S/M/L/XL), paket layanan.
 * Perhitungan otomatis JS Vanilla:
 *   estimasi = harga dasar layanan x multiplier ukuran
 *   (S=1.0, M=1.15, L=1.3, XL=1.5 — lihat assets/js/main.js)
 * Hasil dalam Rupiah + tombol "Booking dengan Paket Ini".
 * =====================================================
 */
$page_title = 'Kalkulator Estimasi Harga';
$meta_desc  = 'Hitung estimasi biaya ceramic coating dan detailing mobil Anda secara instan berdasarkan jenis mobil, ukuran, dan paket layanan.';
$services   = $db->query('SELECT id, name, price FROM services ORDER BY id')->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container" style="max-width:760px">
    <span class="title-line"></span>
    <h1 class="section-title">Kalkulator <span class="text-accent">Estimasi Harga</span></h1>
    <p class="section-sub mb-5">Dapatkan perkiraan biaya dalam hitungan detik. Harga final dikonfirmasi setelah inspeksi kondisi mobil.</p>

    <form id="calcForm" class="cc-card p-4" data-aos="fade-up" onsubmit="return false">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="calcType">Jenis Mobil</label>
          <select class="form-select" id="calcType">
            <option value="">-- Pilih jenis --</option>
            <option value="sedan">Sedan</option>
            <option value="suv">SUV</option>
            <option value="mpv">MPV</option>
            <option value="hatchback">Hatchback</option>
            <option value="pickup">Pickup</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="calcSize">Ukuran Mobil</label>
          <select class="form-select" id="calcSize">
            <option value="S">S — City car / hatchback kecil</option>
            <option value="M" selected>M — Sedan / hatchback besar</option>
            <option value="L">L — MPV / SUV medium</option>
            <option value="XL">XL — SUV besar / pickup double cabin</option>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label" for="calcService">Paket Layanan</label>
          <select class="form-select" id="calcService">
            <option value="">-- Pilih layanan --</option>
            <?php foreach ($services as $s): ?>
              <option value="<?= (int)$s['id'] ?>" data-price="<?= (float)$s['price'] ?>">
                <?= e($s['name']) ?> (mulai <?= format_rupiah($s['price']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Hasil estimasi (diisi JS) -->
      <div id="calcResult" class="d-none mt-4 p-4 text-center" style="background:var(--cc-black);border:1px solid var(--cc-accent)">
        <div class="small text-secondary-light text-uppercase">Estimasi Biaya</div>
        <div id="calcPrice" class="price-tag" style="font-size:2.2rem">Rp 0</div>
        <p class="small text-secondary-light mb-3">*Estimasi awal — harga final menyesuaikan kondisi cat &amp; tingkat kesulitan.</p>
        <a id="calcBookBtn" href="index.php?page=booking" class="btn btn-accent text-uppercase px-4">Booking dengan Paket Ini</a>
      </div>
    </form>
  </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
