<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR 4.9 — BOOKING ONLINE
 * Form: nama, WA, email, merk/tipe/tahun/warna mobil,
 * layanan (dropdown dari DB), tanggal, jam (slot), catatan.
 *
 * VALIDASI SERVER-SIDE:
 * - Semua field wajib tidak boleh kosong
 * - Nomor WA valid (08xx / 628xx, 10-15 digit)
 * - Slot tidak bentrok (cek kuota harian + jam yang terisi)
 * - Tanggal tidak boleh tanggal lampau / tanggal tutup admin
 * - CSRF token
 *
 * Setelah sukses: generate kode booking unik CC-YYYYMMDD-XXXX
 * lalu redirect ke halaman sukses (booking-success).
 * =====================================================
 */
$page_title = 'Booking Online';
$meta_desc  = 'Booking layanan ceramic coating dan detailing mobil di Coating Cepat secara online. Pilih jadwal, dapatkan kode booking, konfirmasi via WhatsApp.';

$services = $db->query('SELECT id, name, price FROM services ORDER BY id')->fetchAll();
$slots    = array_filter(array_map('trim', explode(',', setting($db, 'time_slots', '09:00,10:00,11:00,13:00,14:00,15:00,16:00'))));
$quota    = (int)setting($db, 'daily_quota', 15);

$errors = [];
$old    = [
    'name' => '', 'phone' => '', 'email' => '', 'car_brand' => '', 'car_type' => '',
    'car_year' => '', 'car_color' => '', 'service_id' => (int)($_GET['service'] ?? 0),
    'booking_date' => '', 'booking_time' => '', 'notes' => '',
];

/* ================= PROSES SUBMIT FORM ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify(); // FITUR KEAMANAN: CSRF token

    foreach ($old as $k => $v) { $old[$k] = clean($_POST[$k] ?? ''); }
    $old['service_id'] = (int)$old['service_id'];

    /* --- Validasi field wajib --- */
    $required = [
        'name' => 'Nama lengkap', 'phone' => 'Nomor WhatsApp', 'email' => 'Email',
        'car_brand' => 'Merk mobil', 'car_type' => 'Tipe mobil', 'car_year' => 'Tahun mobil',
        'car_color' => 'Warna mobil', 'booking_date' => 'Tanggal booking', 'booking_time' => 'Jam booking',
    ];
    foreach ($required as $field => $label) {
        if ($old[$field] === '') { $errors[] = $label . ' wajib diisi.'; }
    }
    if ($old['service_id'] <= 0) { $errors[] = 'Layanan wajib dipilih.'; }

    /* --- Validasi nomor WA (08xx / 628xx, 10-15 digit) --- */
    if ($old['phone'] !== '' && !valid_phone($old['phone'])) {
        $errors[] = 'Nomor WhatsApp tidak valid. Gunakan format 08xx atau 628xx (10-15 digit).';
    }

    /* --- Validasi email --- */
    if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    /* --- Validasi tahun mobil --- */
    if ($old['car_year'] !== '' && ((int)$old['car_year'] < 1980 || (int)$old['car_year'] > (int)date('Y') + 1)) {
        $errors[] = 'Tahun mobil tidak valid.';
    }

    /* --- Validasi layanan ada di database --- */
    if ($old['service_id'] > 0) {
        $st = $db->prepare('SELECT COUNT(*) FROM services WHERE id = ?');
        $st->execute([$old['service_id']]);
        if (!$st->fetchColumn()) { $errors[] = 'Layanan yang dipilih tidak ditemukan.'; }
    }

    /* --- Validasi slot jam termasuk slot yang diizinkan admin --- */
    if ($old['booking_time'] !== '' && !in_array($old['booking_time'], $slots, true)) {
        $errors[] = 'Slot jam tidak tersedia.';
    }

    /* --- Validasi tanggal: bukan tanggal lampau --- */
    if ($old['booking_date'] !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $old['booking_date']);
        if (!$d || $d->format('Y-m-d') !== $old['booking_date']) {
            $errors[] = 'Format tanggal tidak valid.';
        } elseif ($old['booking_date'] < date('Y-m-d')) {
            $errors[] = 'Tanggal booking tidak boleh tanggal yang sudah lewat.';
        } else {
            /* --- Validasi tanggal tutup (libur) yang diatur admin --- */
            $st = $db->prepare('SELECT reason FROM schedules WHERE closed_date = ?');
            $st->execute([$old['booking_date']]);
            if ($row = $st->fetch()) {
                $errors[] = 'Tanggal tersebut workshop tutup' . ($row['reason'] ? ' (' . $row['reason'] . ')' : '') . '. Silakan pilih tanggal lain.';
            }

            /* --- Validasi kuota harian (default 15 booking/hari) --- */
            $st = $db->prepare("SELECT COUNT(*) FROM bookings WHERE booking_date = ? AND status <> 'rejected'");
            $st->execute([$old['booking_date']]);
            if ((int)$st->fetchColumn() >= $quota) {
                $errors[] = 'Kuota booking pada tanggal tersebut sudah penuh. Silakan pilih tanggal lain.';
            }

            /* --- Validasi slot jam tidak bentrok --- */
            $st = $db->prepare("SELECT COUNT(*) FROM bookings WHERE booking_date = ? AND booking_time = ? AND status <> 'rejected'");
            $st->execute([$old['booking_date'], $old['booking_time']]);
            if ((int)$st->fetchColumn() > 0) {
                $errors[] = 'Slot jam ' . e($old['booking_time']) . ' pada tanggal tersebut sudah terisi. Silakan pilih jam lain.';
            }
        }
    }

    /* ================= SIMPAN BOOKING ================= */
    if (!$errors) {
        $code = generate_booking_code($db); // kode unik CC-YYYYMMDD-XXXX
        $st = $db->prepare('INSERT INTO bookings
            (booking_code, name, phone, email, car_brand, car_type, car_year, car_color,
             service_id, booking_date, booking_time, notes, status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?, "pending")');
        $st->execute([
            $code, $old['name'], preg_replace('/\D/', '', $old['phone']), $old['email'],
            $old['car_brand'], $old['car_type'], (int)$old['car_year'], $old['car_color'],
            $old['service_id'], $old['booking_date'], $old['booking_time'], $old['notes'],
        ]);
        $_SESSION['last_booking_code'] = $code;
        redirect('index.php?page=booking-success&code=' . urlencode($code));
    }
}

require __DIR__ . '/../includes/header.php';

/* Daftar tanggal tutup -> dipakai untuk disable di datepicker (client-side) */
$closed = $db->query('SELECT closed_date FROM schedules')->fetchAll(PDO::FETCH_COLUMN);
?>
<section class="section">
  <div class="container" style="max-width:820px">
    <span class="title-line"></span>
    <h1 class="section-title">Booking <span class="text-accent">Online</span></h1>
    <p class="section-sub mb-4">Isi data di bawah ini — Anda akan mendapatkan kode booking dan konfirmasi via WhatsApp.</p>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <strong>Booking gagal, mohon periksa kembali:</strong>
        <ul class="mb-0 mt-1 small">
          <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" id="bookingForm" class="cc-card p-4" novalidate>
      <?= csrf_field() ?>
      <h5 class="text-uppercase text-accent mb-3">Data Pemesan</h5>
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Nama Lengkap *</label>
          <input type="text" class="form-control" name="name" value="<?= e($old['name']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Nomor WhatsApp *</label>
          <input type="tel" class="form-control" name="phone" value="<?= e($old['phone']) ?>" placeholder="08xxxxxxxxxx" required>
          <div class="invalid-feedback">Format: 08xx atau 628xx, 10-15 digit.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email *</label>
          <input type="email" class="form-control" name="email" value="<?= e($old['email']) ?>" required>
        </div>
      </div>

      <h5 class="text-uppercase text-accent mb-3">Data Mobil</h5>
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Merk Mobil *</label>
          <input type="text" class="form-control" name="car_brand" value="<?= e($old['car_brand']) ?>" placeholder="Toyota, Honda, ..." required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tipe Mobil *</label>
          <input type="text" class="form-control" name="car_type" value="<?= e($old['car_type']) ?>" placeholder="Avanza, Civic, ..." required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tahun Mobil *</label>
          <input type="number" class="form-control" name="car_year" value="<?= e($old['car_year']) ?>" min="1980" max="<?= date('Y') + 1 ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Warna Mobil *</label>
          <input type="text" class="form-control" name="car_color" value="<?= e($old['car_color']) ?>" required>
        </div>
      </div>

      <h5 class="text-uppercase text-accent mb-3">Jadwal &amp; Layanan</h5>
      <div class="row g-3 mb-4">
        <div class="col-md-12">
          <label class="form-label">Layanan *</label>
          <select class="form-select" name="service_id" required>
            <option value="">-- Pilih layanan --</option>
            <?php foreach ($services as $s): ?>
              <option value="<?= (int)$s['id'] ?>" <?= (int)$old['service_id'] === (int)$s['id'] ? 'selected' : '' ?>>
                <?= e($s['name']) ?> — mulai <?= format_rupiah($s['price']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tanggal Booking *</label>
          <input type="date" class="form-control" name="booking_date" id="bookingDate"
                 value="<?= e($old['booking_date']) ?>" min="<?= date('Y-m-d') ?>" required>
          <small class="text-secondary-light">Tanggal merah/libur workshop otomatis ditolak sistem.</small>
        </div>
        <div class="col-md-6">
          <label class="form-label">Jam Booking (Slot) *</label>
          <select class="form-select" name="booking_time" required>
            <option value="">-- Pilih jam --</option>
            <?php foreach ($slots as $slot): ?>
              <option value="<?= e($slot) ?>" <?= $old['booking_time'] === $slot ? 'selected' : '' ?>><?= e($slot) ?> WIB</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Catatan Tambahan</label>
          <textarea class="form-control" name="notes" rows="3" placeholder="Contoh: ada baret di pintu kanan, mohon perhatian khusus"><?= e($old['notes']) ?></textarea>
        </div>
      </div>

      <button type="submit" class="btn btn-accent btn-lg w-100 text-uppercase">Kirim Booking</button>
      <p class="small text-secondary-light text-center mt-3 mb-0">
        <i class="fa-solid fa-lock me-1"></i>Data Anda aman. Maksimal <?= (int)$quota ?> booking per hari.
      </p>
    </form>
  </div>
</section>

<script>
/* Validasi client-side: tanggal tutup (dari admin) langsung ditolak sebelum submit */
(function () {
  var closedDates = <?= json_encode(array_values($closed)) ?>;
  var input = document.getElementById('bookingDate');
  if (!input) return;
  input.addEventListener('change', function () {
    if (closedDates.indexOf(input.value) !== -1) {
      alert('Maaf, workshop tutup pada tanggal tersebut. Silakan pilih tanggal lain.');
      input.value = '';
    }
  });
})();
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
