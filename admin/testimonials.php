<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR ADMIN — KELOLA TESTIMONI (CRUD)
 * Nama, kendaraan, rating (1-5), komentar, upload foto kendaraan.
 * =====================================================
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'add' || $action === 'edit') {
        $name    = clean($_POST['name'] ?? '');
        $vehicle = clean($_POST['vehicle'] ?? '');
        $rating  = min(5, max(1, (int)($_POST['rating'] ?? 5)));
        $comment = clean($_POST['comment'] ?? '');
        $err = null;
        $photo = upload_image('photo', $err);
        if ($name === '' || $vehicle === '' || $comment === '') {
            $flash = 'Nama, kendaraan, dan komentar wajib diisi.';
        } elseif ($err) {
            $flash = 'Upload foto gagal: ' . $err;
        } elseif ($action === 'add') {
            $db->prepare('INSERT INTO testimonials (name, vehicle, rating, comment, photo) VALUES (?,?,?,?,?)')
               ->execute([$name, $vehicle, $rating, $comment, $photo]);
            $flash = 'Testimoni ditambahkan.';
        } else {
            if ($photo) {
                $db->prepare('UPDATE testimonials SET name=?, vehicle=?, rating=?, comment=?, photo=? WHERE id=?')
                   ->execute([$name, $vehicle, $rating, $comment, $photo, $id]);
            } else {
                $db->prepare('UPDATE testimonials SET name=?, vehicle=?, rating=?, comment=? WHERE id=?')
                   ->execute([$name, $vehicle, $rating, $comment, $id]);
            }
            $flash = 'Testimoni diperbarui.';
        }
    } elseif ($action === 'delete') {
        $st = $db->prepare('SELECT photo FROM testimonials WHERE id = ?');
        $st->execute([$id]);
        if ($p = $st->fetchColumn()) { @unlink(UPLOAD_DIR . $p); }
        $db->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
        $flash = 'Testimoni dihapus.';
    }
}

$items = $db->query('SELECT * FROM testimonials ORDER BY id DESC')->fetchAll();
$admin_title  = 'Kelola Testimoni';
$admin_active = 'testimonials';
require __DIR__ . '/includes/admin_header.php';
?>
<?php if ($flash): ?><div class="alert alert-info alert-dismissible"><?= e($flash) ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php endif; ?>

<p><button class="btn btn-warning" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus mr-1"></i>Tambah Testimoni</button></p>

<div class="card"><div class="card-body table-responsive">
<table class="table table-bordered table-sm datatable">
  <thead><tr><th>Nama</th><th>Kendaraan</th><th>Rating</th><th>Komentar</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($items as $t): ?>
    <tr>
      <td><?= e($t['name']) ?></td>
      <td><?= e($t['vehicle']) ?></td>
      <td><?= (int)$t['rating'] ?> <i class="fas fa-star text-warning"></i></td>
      <td class="small"><?= e(mb_strimwidth($t['comment'], 0, 90, '...')) ?></td>
      <td class="text-nowrap">
        <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#editModal<?= (int)$t['id'] ?>"><i class="fas fa-edit"></i></button>
        <form method="post" class="d-inline confirm-form" data-title="Hapus testimoni ini?" data-icon="warning">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
          <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
        </form>
      </td>
    </tr>
    <div class="modal fade" id="editModal<?= (int)$t['id'] ?>" tabindex="-1">
      <div class="modal-dialog">
        <form method="post" enctype="multipart/form-data" class="modal-content">
          <?= csrf_field() ?><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
          <div class="modal-header py-2"><h6 class="modal-title">Edit Testimoni</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
          <div class="modal-body">
            <div class="form-group"><label class="small">Nama *</label><input type="text" name="name" class="form-control form-control-sm" value="<?= e($t['name']) ?>" required></div>
            <div class="form-group"><label class="small">Kendaraan *</label><input type="text" name="vehicle" class="form-control form-control-sm" value="<?= e($t['vehicle']) ?>" required></div>
            <div class="form-group"><label class="small">Rating</label>
              <select name="rating" class="form-control form-control-sm">
                <?php for ($r = 5; $r >= 1; $r--): ?><option value="<?= $r ?>" <?= (int)$t['rating'] === $r ? 'selected' : '' ?>><?= $r ?> bintang</option><?php endfor; ?>
              </select>
            </div>
            <div class="form-group"><label class="small">Komentar *</label><textarea name="comment" class="form-control form-control-sm" rows="3" required><?= e($t['comment']) ?></textarea></div>
            <div class="form-group mb-0"><label class="small">Ganti Foto Kendaraan (opsional)</label><input type="file" name="photo" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp"></div>
          </div>
          <div class="modal-footer py-2"><button class="btn btn-sm btn-warning">Simpan</button></div>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
  </tbody>
</table>
</div></div>

<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <?= csrf_field() ?><input type="hidden" name="action" value="add">
      <div class="modal-header py-2"><h6 class="modal-title">Tambah Testimoni</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group"><label class="small">Nama *</label><input type="text" name="name" class="form-control form-control-sm" required></div>
        <div class="form-group"><label class="small">Kendaraan *</label><input type="text" name="vehicle" class="form-control form-control-sm" placeholder="Toyota Avanza 2022" required></div>
        <div class="form-group"><label class="small">Rating</label>
          <select name="rating" class="form-control form-control-sm">
            <?php for ($r = 5; $r >= 1; $r--): ?><option value="<?= $r ?>"><?= $r ?> bintang</option><?php endfor; ?>
          </select>
        </div>
        <div class="form-group"><label class="small">Komentar *</label><textarea name="comment" class="form-control form-control-sm" rows="3" required></textarea></div>
        <div class="form-group mb-0"><label class="small">Foto Kendaraan (opsional, maks 2MB)</label><input type="file" name="photo" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp"></div>
      </div>
      <div class="modal-footer py-2"><button class="btn btn-sm btn-warning">Tambah</button></div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
