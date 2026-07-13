<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR ADMIN — KELOLA PENGATURAN WEBSITE
 * Edit: nama usaha, nomor WhatsApp, Instagram, alamat,
 * Maps URL (embed), logo (upload), jam operasional,
 * meta title & description default, statistik homepage.
 * Disimpan di tabel settings (key-value).
 * Bonus: ganti password admin.
 * =====================================================
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$flash = '';

/* Daftar key yang boleh diedit lewat form ini */
$editable = [
    'site_name'        => 'Nama Usaha',
    'whatsapp'         => 'Nomor WhatsApp',
    'instagram'        => 'Instagram (tanpa @)',
    'address'          => 'Alamat',
    'maps_url'         => 'Google Maps Embed URL',
    'open_hours'       => 'Jam Operasional',
    'meta_title'       => 'Meta Title Default',
    'meta_description' => 'Meta Description Default',
    'stat_cars'        => 'Statistik: Mobil Dikerjakan',
    'stat_years'       => 'Statistik: Tahun Pengalaman',
    'stat_rating'      => 'Statistik: Rating',
    'stat_customers'   => 'Statistik: Pelanggan Puas',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'save_settings') {
        $up = $db->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        foreach (array_keys($editable) as $key) {
            if (isset($_POST[$key])) {
                $val = clean($_POST[$key]);
                /* validasi khusus nomor WA */
                if ($key === 'whatsapp' && $val !== '' && !valid_phone($val)) {
                    $flash .= 'Nomor WhatsApp tidak valid, tidak disimpan. ';
                    continue;
                }
                $up->execute([$key, $val]);
            }
        }
        /* Upload logo (opsional) */
        $err = null;
        $logo = upload_image('logo', $err);
        if ($err) { $flash .= 'Upload logo gagal: ' . $err; }
        elseif ($logo) { $up->execute(['logo', $logo]); }
        $flash = ($flash ?: '') . ' Pengaturan disimpan.';
    } elseif ($action === 'change_password') {
        /* Ganti password admin (wajib password lama benar) */
        $old = (string)($_POST['old_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');
        $st = $db->prepare('SELECT password FROM admins WHERE id = ?');
        $st->execute([(int)$_SESSION['admin_id']]);
        $hash = $st->fetchColumn();
        if (!$hash || !password_verify($old, $hash)) {
            $flash = 'Password lama salah.';
        } elseif (strlen($new) < 8) {
            $flash = 'Password baru minimal 8 karakter.';
        } else {
            $db->prepare('UPDATE admins SET password = ? WHERE id = ?')
               ->execute([password_hash($new, PASSWORD_DEFAULT), (int)$_SESSION['admin_id']]);
            $flash = 'Password berhasil diganti.';
        }
    }
}

/* Baca nilai terbaru langsung dari DB */
$values = [];
foreach ($db->query('SELECT setting_key, setting_value FROM settings') as $row) {
    $values[$row['setting_key']] = $row['setting_value'];
}

$admin_title  = 'Pengaturan Website';
$admin_active = 'settings';
require __DIR__ . '/includes/admin_header.php';
?>
<?php if ($flash): ?><div class="alert alert-info alert-dismissible"><?= e($flash) ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php endif; ?>

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-cog mr-2"></i>Pengaturan Umum</h3></div>
      <form method="post" enctype="multipart/form-data" class="card-body">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_settings">
        <div class="row">
          <?php foreach ($editable as $key => $label):
            $val = $values[$key] ?? '';
            $is_long = in_array($key, ['maps_url', 'meta_description', 'address'], true);
          ?>
          <div class="<?= $is_long ? 'col-12' : 'col-md-6' ?> form-group">
            <label class="small"><?= e($label) ?></label>
            <?php if ($key === 'meta_description'): ?>
              <textarea name="<?= e($key) ?>" class="form-control form-control-sm" rows="2"><?= e($val) ?></textarea>
            <?php else: ?>
              <input type="text" name="<?= e($key) ?>" class="form-control form-control-sm" value="<?= e($val) ?>">
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
          <div class="col-12 form-group">
            <label class="small">Logo (jpg/png/webp, maks 2MB — kosongkan jika tidak diganti)</label><br>
            <?php if (!empty($values['logo'])): ?>
              <img src="<?= e(UPLOAD_URL . $values['logo']) ?>" alt="Logo" style="height:48px" class="mb-2 bg-dark p-1">
            <?php endif; ?>
            <input type="file" name="logo" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp">
          </div>
        </div>
        <button class="btn btn-warning"><i class="fas fa-save mr-1"></i>Simpan Pengaturan</button>
      </form>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header"><h3 class="card-title"><i class="fas fa-key mr-2"></i>Ganti Password Admin</h3></div>
      <form method="post" class="card-body">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="change_password">
        <div class="form-group"><label class="small">Password Lama</label><input type="password" name="old_password" class="form-control form-control-sm" required></div>
        <div class="form-group"><label class="small">Password Baru (min. 8 karakter)</label><input type="password" name="new_password" class="form-control form-control-sm" minlength="8" required></div>
        <button class="btn btn-warning btn-sm">Ganti Password</button>
        <p class="small text-muted mt-2 mb-0">Disarankan segera mengganti password default <code>admin123</code> setelah login pertama.</p>
      </form>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
