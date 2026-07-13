<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR ADMIN — KELOLA LAYANAN (CRUD)
 * Field: nama, harga, deskripsi, benefit (multi-baris),
 * estimasi pengerjaan, kategori galeri, thumbnail (upload).
 * Semua aksi POST + CSRF, hapus dengan SweetAlert2.
 * =====================================================
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$flash = '';
$err = null;

/* ---- helper slug sederhana ---- */
function make_slug($db, $name, $exclude_id = 0) {
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
    $base = $slug; $n = 1;
    while (true) {
        $st = $db->prepare('SELECT COUNT(*) FROM services WHERE slug = ? AND id <> ?');
        $st->execute([$slug, $exclude_id]);
        if (!$st->fetchColumn()) break;
        $slug = $base . '-' . (++$n);
    }
    return $slug;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'add' || $action === 'edit') {
        $name     = clean($_POST['name'] ?? '');
        $price    = (float)($_POST['price'] ?? 0);
        $desc     = clean($_POST['description'] ?? '');
        $benefits = clean($_POST['benefits'] ?? '');
        $duration = clean($_POST['duration'] ?? '');
        $category = clean($_POST['category'] ?? 'coating');
        if (!in_array($category, ['coating','paint-correction','interior','headlamp','engine'], true)) $category = 'coating';

        if ($name === '' || $desc === '' || $price <= 0) {
            $flash = 'Nama, deskripsi, dan harga wajib diisi.';
        } else {
            $thumb = upload_image('thumbnail', $err); // upload thumbnail (opsional)
            if ($err) { $flash = 'Upload thumbnail gagal: ' . $err; }
            else {
                if ($action === 'add') {
                    $db->prepare('INSERT INTO services (name, slug, price, description, benefits, duration, category, thumbnail) VALUES (?,?,?,?,?,?,?,?)')
                       ->execute([$name, make_slug($db, $name), $price, $desc, $benefits, $duration, $category, $thumb]);
                    $flash = 'Layanan ditambahkan.';
                } else {
                    if ($thumb) {
                        $db->prepare('UPDATE services SET name=?, slug=?, price=?, description=?, benefits=?, duration=?, category=?, thumbnail=? WHERE id=?')
                           ->execute([$name, make_slug($db, $name, $id), $price, $desc, $benefits, $duration, $category, $thumb, $id]);
                    } else {
                        $db->prepare('UPDATE services SET name=?, slug=?, price=?, description=?, benefits=?, duration=?, category=? WHERE id=?')
                           ->execute([$name, make_slug($db, $name, $id), $price, $desc, $benefits, $duration, $category, $id]);
                    }
                    $flash = 'Layanan diperbarui.';
                }
            }
        }
    } elseif ($action === 'delete') {
        /* Cegah hapus layanan yang masih dipakai booking (FK RESTRICT) */
        try {
            $db->prepare('DELETE FROM services WHERE id = ?')->execute([$id]);
            $flash = 'Layanan dihapus.';
        } catch (PDOException $e) {
            $flash = 'Gagal menghapus: layanan masih memiliki data booking.';
        }
    }
}

$services = $db->query('SELECT * FROM services ORDER BY id')->fetchAll();
$cats = ['coating'=>'Coating','paint-correction'=>'Paint Correction','interior'=>'Interior','headlamp'=>'Headlamp','engine'=>'Engine'];

$admin_title  = 'Kelola Layanan';
$admin_active = 'services';
require __DIR__ . '/includes/admin_header.php';
?>
<?php if ($flash): ?><div class="alert alert-info alert-dismissible"><?= e($flash) ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php endif; ?>

<p><button class="btn btn-warning" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus mr-1"></i>Tambah Layanan</button></p>

<div class="card"><div class="card-body table-responsive">
<table class="table table-bordered table-sm datatable">
  <thead><tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Harga Mulai</th><th>Estimasi</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($services as $s): ?>
    <tr>
      <td><?= (int)$s['id'] ?></td>
      <td><?= e($s['name']) ?></td>
      <td><?= e($cats[$s['category']] ?? $s['category']) ?></td>
      <td><?= format_rupiah($s['price']) ?></td>
      <td><?= e($s['duration']) ?></td>
      <td class="text-nowrap">
        <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#editModal<?= (int)$s['id'] ?>"><i class="fas fa-edit"></i> Edit</button>
        <form method="post" class="d-inline confirm-form" data-title="Hapus layanan ini?" data-text="<?= e($s['name']) ?>" data-icon="warning">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
          <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i> Hapus</button>
        </form>
      </td>
    </tr>

    <!-- Modal Edit Layanan -->
    <div class="modal fade" id="editModal<?= (int)$s['id'] ?>" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <form method="post" enctype="multipart/form-data" class="modal-content">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
          <div class="modal-header py-2"><h6 class="modal-title">Edit: <?= e($s['name']) ?></h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 form-group"><label class="small">Nama *</label><input type="text" name="name" class="form-control form-control-sm" value="<?= e($s['name']) ?>" required></div>
              <div class="col-md-3 form-group"><label class="small">Harga Mulai (Rp) *</label><input type="number" name="price" class="form-control form-control-sm" value="<?= (float)$s['price'] ?>" min="0" required></div>
              <div class="col-md-3 form-group"><label class="small">Estimasi</label><input type="text" name="duration" class="form-control form-control-sm" value="<?= e($s['duration']) ?>"></div>
              <div class="col-md-4 form-group"><label class="small">Kategori Galeri</label>
                <select name="category" class="form-control form-control-sm">
                  <?php foreach ($cats as $k => $v): ?><option value="<?= $k ?>" <?= $s['category'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-8 form-group"><label class="small">Thumbnail (jpg/png/webp, maks 2MB — kosongkan jika tidak diganti)</label><input type="file" name="thumbnail" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp"></div>
              <div class="col-12 form-group"><label class="small">Deskripsi *</label><textarea name="description" class="form-control form-control-sm" rows="3" required><?= e($s['description']) ?></textarea></div>
              <div class="col-12 form-group mb-0"><label class="small">Benefit (satu per baris)</label><textarea name="benefits" class="form-control form-control-sm" rows="4"><?= e($s['benefits']) ?></textarea></div>
            </div>
          </div>
          <div class="modal-footer py-2"><button class="btn btn-sm btn-warning">Simpan</button></div>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
  </tbody>
</table>
</div></div>

<!-- Modal Tambah Layanan -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      <div class="modal-header py-2"><h6 class="modal-title">Tambah Layanan</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6 form-group"><label class="small">Nama *</label><input type="text" name="name" class="form-control form-control-sm" required></div>
          <div class="col-md-3 form-group"><label class="small">Harga Mulai (Rp) *</label><input type="number" name="price" class="form-control form-control-sm" min="0" required></div>
          <div class="col-md-3 form-group"><label class="small">Estimasi</label><input type="text" name="duration" class="form-control form-control-sm" placeholder="mis. 1-2 hari"></div>
          <div class="col-md-4 form-group"><label class="small">Kategori Galeri</label>
            <select name="category" class="form-control form-control-sm">
              <?php foreach ($cats as $k => $v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-8 form-group"><label class="small">Thumbnail (jpg/png/webp, maks 2MB)</label><input type="file" name="thumbnail" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp"></div>
          <div class="col-12 form-group"><label class="small">Deskripsi *</label><textarea name="description" class="form-control form-control-sm" rows="3" required></textarea></div>
          <div class="col-12 form-group mb-0"><label class="small">Benefit (satu per baris)</label><textarea name="benefits" class="form-control form-control-sm" rows="4"></textarea></div>
        </div>
      </div>
      <div class="modal-footer py-2"><button class="btn btn-sm btn-warning">Tambah</button></div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
