<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR ADMIN — KELOLA GALERI (CRUD)
 * Upload MULTIPLE image sekaligus, judul, deskripsi, kategori.
 * =====================================================
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$flash = '';
$cats = ['coating'=>'Coating','paint-correction'=>'Paint Correction','interior'=>'Interior','headlamp'=>'Headlamp','engine'=>'Engine'];

/* Helper upload multiple: validasi sama dengan upload_image() */
function upload_multiple($field, &$errors) {
    $saved = [];
    if (empty($_FILES[$field]['name'][0])) return $saved;
    $count = count($_FILES[$field]['name']);
    for ($i = 0; $i < $count; $i++) {
        // bungkus tiap file agar bisa dipakai upload_image()
        $_FILES['__single'] = [
            'name'     => $_FILES[$field]['name'][$i],
            'type'     => $_FILES[$field]['type'][$i],
            'tmp_name' => $_FILES[$field]['tmp_name'][$i],
            'error'    => $_FILES[$field]['error'][$i],
            'size'     => $_FILES[$field]['size'][$i],
        ];
        $err = null;
        $name = upload_image('__single', $err);
        if ($name) { $saved[] = $name; }
        elseif ($err) { $errors[] = $_FILES[$field]['name'][$i] . ': ' . $err; }
    }
    unset($_FILES['__single']);
    return $saved;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'add') {
        $title = clean($_POST['title'] ?? '');
        $desc  = clean($_POST['description'] ?? '');
        $cat   = clean($_POST['category'] ?? 'coating');
        if (!isset($cats[$cat])) $cat = 'coating';
        $errs = [];
        $files = upload_multiple('images', $errs);
        if ($title === '' || !$files) {
            $flash = 'Judul wajib diisi dan minimal 1 foto valid. ' . implode(' ', $errs);
        } else {
            $st = $db->prepare('INSERT INTO gallery (title, description, category, image) VALUES (?,?,?,?)');
            foreach ($files as $idx => $f) {
                $st->execute([$title . (count($files) > 1 ? ' #' . ($idx + 1) : ''), $desc, $cat, $f]);
            }
            $flash = count($files) . ' foto berhasil ditambahkan. ' . implode(' ', $errs);
        }
    } elseif ($action === 'edit') {
        $title = clean($_POST['title'] ?? '');
        $desc  = clean($_POST['description'] ?? '');
        $cat   = clean($_POST['category'] ?? 'coating');
        if (!isset($cats[$cat])) $cat = 'coating';
        $err = null;
        $img = upload_image('image', $err);
        if ($err) { $flash = 'Upload gagal: ' . $err; }
        else {
            if ($img) {
                $db->prepare('UPDATE gallery SET title=?, description=?, category=?, image=? WHERE id=?')->execute([$title, $desc, $cat, $img, $id]);
            } else {
                $db->prepare('UPDATE gallery SET title=?, description=?, category=? WHERE id=?')->execute([$title, $desc, $cat, $id]);
            }
            $flash = 'Foto galeri diperbarui.';
        }
    } elseif ($action === 'delete') {
        $st = $db->prepare('SELECT image FROM gallery WHERE id = ?');
        $st->execute([$id]);
        if ($img = $st->fetchColumn()) { @unlink(UPLOAD_DIR . $img); }
        $db->prepare('DELETE FROM gallery WHERE id = ?')->execute([$id]);
        $flash = 'Foto galeri dihapus.';
    }
}

$items = $db->query('SELECT * FROM gallery ORDER BY created_at DESC, id DESC')->fetchAll();
$admin_title  = 'Kelola Galeri';
$admin_active = 'gallery';
require __DIR__ . '/includes/admin_header.php';
?>
<?php if ($flash): ?><div class="alert alert-info alert-dismissible"><?= e($flash) ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php endif; ?>

<p><button class="btn btn-warning" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus mr-1"></i>Tambah Foto (Multiple)</button></p>

<div class="card"><div class="card-body table-responsive">
<table class="table table-bordered table-sm datatable">
  <thead><tr><th>Foto</th><th>Judul</th><th>Kategori</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($items as $g): ?>
    <tr>
      <td style="width:90px"><img src="<?= img_url($g['image'], $g['title']) ?>" alt="<?= e($g['title']) ?>" style="width:80px;height:60px;object-fit:cover"></td>
      <td><?= e($g['title']) ?></td>
      <td><?= e($cats[$g['category']] ?? $g['category']) ?></td>
      <td class="small"><?= e($g['description']) ?></td>
      <td class="text-nowrap">
        <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#editModal<?= (int)$g['id'] ?>"><i class="fas fa-edit"></i></button>
        <form method="post" class="d-inline confirm-form" data-title="Hapus foto ini?" data-icon="warning">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
          <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
        </form>
      </td>
    </tr>
    <div class="modal fade" id="editModal<?= (int)$g['id'] ?>" tabindex="-1">
      <div class="modal-dialog">
        <form method="post" enctype="multipart/form-data" class="modal-content">
          <?= csrf_field() ?><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
          <div class="modal-header py-2"><h6 class="modal-title">Edit Foto</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
          <div class="modal-body">
            <div class="form-group"><label class="small">Judul *</label><input type="text" name="title" class="form-control form-control-sm" value="<?= e($g['title']) ?>" required></div>
            <div class="form-group"><label class="small">Deskripsi</label><input type="text" name="description" class="form-control form-control-sm" value="<?= e($g['description']) ?>"></div>
            <div class="form-group"><label class="small">Kategori</label>
              <select name="category" class="form-control form-control-sm">
                <?php foreach ($cats as $k => $v): ?><option value="<?= $k ?>" <?= $g['category'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="form-group mb-0"><label class="small">Ganti Foto (kosongkan jika tidak diganti)</label><input type="file" name="image" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp"></div>
          </div>
          <div class="modal-footer py-2"><button class="btn btn-sm btn-warning">Simpan</button></div>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
  </tbody>
</table>
</div></div>

<!-- Modal Tambah (upload multiple) -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <?= csrf_field() ?><input type="hidden" name="action" value="add">
      <div class="modal-header py-2"><h6 class="modal-title">Tambah Foto Galeri (bisa pilih banyak)</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group"><label class="small">Judul *</label><input type="text" name="title" class="form-control form-control-sm" required></div>
        <div class="form-group"><label class="small">Deskripsi</label><input type="text" name="description" class="form-control form-control-sm"></div>
        <div class="form-group"><label class="small">Kategori</label>
          <select name="category" class="form-control form-control-sm">
            <?php foreach ($cats as $k => $v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group mb-0"><label class="small">Foto (boleh lebih dari satu, maks 2MB/file)</label>
          <input type="file" name="images[]" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp" multiple required></div>
      </div>
      <div class="modal-footer py-2"><button class="btn btn-sm btn-warning">Upload</button></div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
