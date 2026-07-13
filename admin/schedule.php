<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR ADMIN — KELOLA JADWAL
 * - Tutup tanggal tertentu (libur) + alasan
 * - Atur kuota booking harian (default 15)
 * - Atur slot jam operasional yang tersedia
 *   (kuota & slot disimpan di tabel settings)
 * =====================================================
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'close_date') {
        /* Tambah tanggal tutup */
        $date   = clean($_POST['closed_date'] ?? '');
        $reason = clean($_POST['reason'] ?? '');
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            $flash = 'Tanggal tidak valid.';
        } else {
            try {
                $db->prepare('INSERT INTO schedules (closed_date, reason) VALUES (?,?)')->execute([$date, $reason]);
                $flash = 'Tanggal ' . e($date) . ' ditandai tutup.';
            } catch (PDOException $e) {
                $flash = 'Tanggal tersebut sudah ada di daftar tutup.';
            }
        }
    } elseif ($action === 'open_date') {
        /* Hapus tanggal tutup (buka kembali) */
        $db->prepare('DELETE FROM schedules WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
        $flash = 'Tanggal dibuka kembali.';
    } elseif ($action === 'save_quota') {
        /* Simpan kuota harian + slot jam ke tabel settings */
        $quota = max(1, (int)($_POST['daily_quota'] ?? 15));
        $slots_raw = clean($_POST['time_slots'] ?? '');
        // validasi format tiap slot HH:MM
        $slots = array_filter(array_map('trim', explode(',', $slots_raw)), function ($s) {
            return preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $s);
        });
        if (!$slots) {
            $flash = 'Slot jam tidak valid. Gunakan format HH:MM dipisah koma, contoh: 09:00,10:00,13:00';
        } else {
            $up = $db->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
            $up->execute(['daily_quota', (string)$quota]);
            $up->execute(['time_slots', implode(',', $slots)]);
            $flash = 'Kuota harian & slot jam disimpan.';
        }
    }
}

$closed = $db->query('SELECT * FROM schedules ORDER BY closed_date')->fetchAll();
/* baca ulang nilai terbaru langsung dari DB (hindari cache get_settings) */
$st = $db->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
$st->execute(['daily_quota']); $quota_val = $st->fetchColumn() ?: '15';
$st->execute(['time_slots']);  $slots_val = $st->fetchColumn() ?: '09:00,10:00,11:00,13:00,14:00,15:00,16:00';

$admin_title  = 'Kelola Jadwal';
$admin_active = 'schedule';
require __DIR__ . '/includes/admin_header.php';
?>
<?php if ($flash): ?><div class="alert alert-info alert-dismissible"><?= e($flash) ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php endif; ?>

<div class="row">
  <!-- Kuota & slot jam -->
  <div class="col-md-5">
    <div class="card">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-sliders-h mr-2"></i>Kuota &amp; Slot Jam</h3></div>
      <form method="post" class="card-body">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_quota">
        <div class="form-group">
          <label class="small">Kuota Booking per Hari</label>
          <input type="number" name="daily_quota" class="form-control" min="1" value="<?= e($quota_val) ?>" required>
        </div>
        <div class="form-group">
          <label class="small">Slot Jam Operasional (format HH:MM, pisah dengan koma)</label>
          <input type="text" name="time_slots" class="form-control" value="<?= e($slots_val) ?>" required>
          <small class="text-muted">Contoh: 09:00,10:00,11:00,13:00,14:00,15:00,16:00</small>
        </div>
        <button class="btn btn-warning">Simpan</button>
      </form>
    </div>
  </div>

  <!-- Tanggal tutup -->
  <div class="col-md-7">
    <div class="card">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-calendar-times mr-2"></i>Tanggal Tutup / Libur</h3></div>
      <div class="card-body">
        <form method="post" class="form-inline mb-3">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="close_date">
          <input type="date" name="closed_date" class="form-control form-control-sm mr-2" min="<?= date('Y-m-d') ?>" required>
          <input type="text" name="reason" class="form-control form-control-sm mr-2" placeholder="Alasan (opsional)">
          <button class="btn btn-sm btn-warning"><i class="fas fa-plus mr-1"></i>Tutup Tanggal</button>
        </form>
        <table class="table table-sm table-bordered">
          <thead><tr><th>Tanggal</th><th>Alasan</th><th style="width:90px">Aksi</th></tr></thead>
          <tbody>
            <?php foreach ($closed as $c): ?>
            <tr>
              <td><?= e(date('d-m-Y', strtotime($c['closed_date']))) ?></td>
              <td><?= e($c['reason'] ?: '-') ?></td>
              <td>
                <form method="post" class="confirm-form" data-title="Buka kembali tanggal ini?" data-icon="question">
                  <?= csrf_field() ?><input type="hidden" name="action" value="open_date"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                  <button class="btn btn-xs btn-success">Buka</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$closed): ?><tr><td colspan="3" class="text-muted small">Belum ada tanggal tutup.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
