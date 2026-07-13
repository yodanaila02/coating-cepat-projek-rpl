<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR ADMIN — KELOLA BOOKING (DataTables)
 * - Aksi: Approve, Reject, Reschedule (ubah tanggal/jam),
 *   Tandai Selesai (semua dengan konfirmasi SweetAlert2 + CSRF)
 * - Filter tanggal & status, search nama/WA via DataTables
 * - Tombol shortcut "Chat WA" per booking -> wa.me dengan
 *   template pesan konfirmasi otomatis
 * =====================================================
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$flash = '';

/* ================= PROSES AKSI (POST + CSRF) ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'approve') {
        $db->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?")->execute([$id]);
        $flash = 'Booking dikonfirmasi.';
    } elseif ($action === 'reject') {
        $db->prepare("UPDATE bookings SET status = 'rejected' WHERE id = ?")->execute([$id]);
        $flash = 'Booking ditolak.';
    } elseif ($action === 'done') {
        $db->prepare("UPDATE bookings SET status = 'done' WHERE id = ?")->execute([$id]);
        $flash = 'Booking ditandai selesai.';
    } elseif ($action === 'reschedule') {
        /* Reschedule: validasi tanggal & jam baru */
        $new_date = clean($_POST['new_date'] ?? '');
        $new_time = clean($_POST['new_time'] ?? '');
        $d = DateTime::createFromFormat('Y-m-d', $new_date);
        if ($d && $d->format('Y-m-d') === $new_date && $new_date >= date('Y-m-d') && $new_time !== '') {
            $db->prepare("UPDATE bookings SET booking_date = ?, booking_time = ?, status = 'rescheduled' WHERE id = ?")
               ->execute([$new_date, $new_time, $id]);
            $flash = 'Jadwal booking berhasil diubah.';
        } else {
            $flash = 'Reschedule gagal: tanggal/jam tidak valid.';
        }
    }
}

/* ================= FILTER TANGGAL & STATUS ================= */
$f_date   = clean($_GET['f_date'] ?? '');
$f_status = clean($_GET['f_status'] ?? '');
$where = [];
$params = [];
if ($f_date !== '')   { $where[] = 'b.booking_date = ?'; $params[] = $f_date; }
if ($f_status !== '' && in_array($f_status, ['pending','confirmed','rescheduled','done','rejected'], true)) {
    $where[] = 'b.status = ?'; $params[] = $f_status;
}
$sql = 'SELECT b.*, s.name AS service_name FROM bookings b JOIN services s ON s.id = b.service_id'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
     . ' ORDER BY b.created_at DESC';
$st = $db->prepare($sql);
$st->execute($params);
$bookings = $st->fetchAll();

$slots = array_filter(array_map('trim', explode(',', setting($db, 'time_slots', '09:00,10:00,11:00,13:00,14:00,15:00,16:00'))));
$site_name = setting($db, 'site_name', 'Coating Cepat');

$admin_title  = 'Kelola Booking';
$admin_active = 'bookings';
require __DIR__ . '/includes/admin_header.php';
?>
<?php if ($flash): ?><div class="alert alert-info alert-dismissible"><?= e($flash) ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php endif; ?>

<!-- Filter tanggal & status -->
<div class="card">
  <div class="card-body py-2">
    <form method="get" class="form-inline">
      <label class="mr-2 small">Tanggal</label>
      <input type="date" name="f_date" class="form-control form-control-sm mr-3" value="<?= e($f_date) ?>">
      <label class="mr-2 small">Status</label>
      <select name="f_status" class="form-control form-control-sm mr-3">
        <option value="">Semua</option>
        <?php foreach (['pending'=>'Pending','confirmed'=>'Dikonfirmasi','rescheduled'=>'Dijadwal Ulang','done'=>'Selesai','rejected'=>'Ditolak'] as $k => $v): ?>
          <option value="<?= $k ?>" <?= $f_status === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-sm btn-warning mr-2" type="submit"><i class="fas fa-filter mr-1"></i>Filter</button>
      <a href="bookings.php" class="btn btn-sm btn-outline-secondary">Reset</a>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body table-responsive">
    <table class="table table-bordered table-sm datatable">
      <thead>
        <tr>
          <th>Kode</th><th>Nama / WA</th><th>Mobil</th><th>Layanan</th>
          <th>Jadwal</th><th>Status</th><th style="min-width:230px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($bookings as $b):
          /* Template pesan WA konfirmasi dari admin ke pelanggan */
          $wa_msg = "Halo {$b['name']}, kami dari {$site_name}.\n"
                  . "Booking Anda:\nKode: {$b['booking_code']}\nLayanan: {$b['service_name']}\n"
                  . "Jadwal: " . date('d-m-Y', strtotime($b['booking_date'])) . " jam {$b['booking_time']} WIB\n"
                  . "Status: {$b['status']}\nTerima kasih.";
        ?>
        <tr>
          <td class="text-nowrap"><strong><?= e($b['booking_code']) ?></strong><br><small class="text-muted"><?= e(date('d-m-y H:i', strtotime($b['created_at']))) ?></small></td>
          <td><?= e($b['name']) ?><br><small class="text-muted"><?= e($b['phone']) ?></small></td>
          <td class="small"><?= e($b['car_brand'] . ' ' . $b['car_type'] . ' ' . $b['car_year']) ?><br><small class="text-muted"><?= e($b['car_color']) ?></small></td>
          <td class="small"><?= e($b['service_name']) ?></td>
          <td class="text-nowrap small"><?= e(date('d-m-Y', strtotime($b['booking_date']))) ?><br><?= e($b['booking_time']) ?> WIB</td>
          <td><?= status_badge($b['status']) ?></td>
          <td>
            <!-- Tombol Chat WA (template konfirmasi) -->
            <a href="<?= e(wa_link($b['phone'], $wa_msg)) ?>" target="_blank" rel="noopener" class="btn btn-xs btn-success mb-1" title="Chat WhatsApp"><i class="fab fa-whatsapp"></i> WA</a>

            <?php if (in_array($b['status'], ['pending', 'rescheduled'], true)): ?>
            <form method="post" class="d-inline confirm-form" data-title="Konfirmasi booking ini?" data-icon="question">
              <?= csrf_field() ?><input type="hidden" name="action" value="approve"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
              <button class="btn btn-xs btn-primary mb-1"><i class="fas fa-check"></i> Approve</button>
            </form>
            <?php endif; ?>

            <?php if (!in_array($b['status'], ['done', 'rejected'], true)): ?>
            <button type="button" class="btn btn-xs btn-warning mb-1" data-toggle="modal" data-target="#resModal<?= (int)$b['id'] ?>"><i class="fas fa-clock"></i> Reschedule</button>

            <form method="post" class="d-inline confirm-form" data-title="Tandai booking selesai?" data-icon="success">
              <?= csrf_field() ?><input type="hidden" name="action" value="done"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
              <button class="btn btn-xs btn-success mb-1"><i class="fas fa-check-double"></i> Selesai</button>
            </form>

            <form method="post" class="d-inline confirm-form" data-title="Tolak booking ini?" data-text="Status akan menjadi rejected." data-icon="warning">
              <?= csrf_field() ?><input type="hidden" name="action" value="reject"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
              <button class="btn btn-xs btn-danger mb-1"><i class="fas fa-times"></i> Reject</button>
            </form>
            <?php endif; ?>

            <!-- Modal Reschedule (ubah tanggal & jam) -->
            <div class="modal fade" id="resModal<?= (int)$b['id'] ?>" tabindex="-1">
              <div class="modal-dialog modal-sm">
                <form method="post" class="modal-content">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="reschedule">
                  <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                  <div class="modal-header py-2"><h6 class="modal-title">Reschedule <?= e($b['booking_code']) ?></h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button></div>
                  <div class="modal-body">
                    <div class="form-group">
                      <label class="small">Tanggal Baru</label>
                      <input type="date" name="new_date" class="form-control form-control-sm" min="<?= date('Y-m-d') ?>" value="<?= e($b['booking_date']) ?>" required>
                    </div>
                    <div class="form-group mb-0">
                      <label class="small">Jam Baru</label>
                      <select name="new_time" class="form-control form-control-sm" required>
                        <?php foreach ($slots as $slot): ?>
                          <option value="<?= e($slot) ?>" <?= $b['booking_time'] === $slot ? 'selected' : '' ?>><?= e($slot) ?> WIB</option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer py-2">
                    <button type="submit" class="btn btn-sm btn-warning">Simpan Jadwal</button>
                  </div>
                </form>
              </div>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
