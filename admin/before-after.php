<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR ADMIN — KELOLA BEFORE-AFTER (CRUD)
 * Upload foto BEFORE + foto AFTER per item, judul,
 * layanan terkait.
 * =====================================================
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'add') {
        $title = clean($_POST['title'] ?? '');
        $sid   = (int)($_POST['service_id'] ?? 0) ?: null;
        $e1 = $e2 = null;
        $before = upload_image('before_image', $e1);
        $after  = upload_image('after_image', $e2);
        if ($title === '' || !$before || !$after) {
            $flash = 'Judul, foto before, dan foto after wajib diisi. ' . ($e1 ?: '') . ' ' . ($e2 ?: '');
        } else {
            $db->prepare('INSERT INTO before_after (title, service_id, before_image, after_image) VALUES (?,?,?,?)')
               ->execute([$title, $sid, $before, $after]);
            $flash = 'Item before-after ditambahkan.';
        }
    } elseif ($action === 'edit') {
        $title = clean($_POST['title'] ?? '');
        $sid   = (int)($_POST['service_id'] ?? 0) ?: null;
        $e1 = $e2 = null;
        $before = upload_image('before_image', $e1);
        $after  = upload_image('after_image', $e2);
        if ($e1 || $e2) { $flash = 'Upload gagal: ' . ($e1 ?: '') . ' ' . ($e2 ?: ''); }
        else {
            $db->prepare('UPDATE before_after SET title=?, service_id=? WHERE id=?')->execute([$title, $sid, $id]);
            if ($before) { $db->prepare('UPDATE before_after SET before_image=? WHERE id=?')->execute([$before, $id]); }
            if ($after)  { $db->prepare('UPDATE before_after SET after_image=? WHERE id=?')->execute([$after, $id]); }
            $flash = 'Item before-after diperbarui.';
        }
    } elseif ($action === 'delete') {
        $st = $db->prepare('SELECT before_image, after_image FROM before_after WHERE id = ?');
        $st->execute([$id]);
        if ($row = $st->fetch()) { @unlink(UPLOAD_DIR . $row['before_image']); @unlink(UPLOAD_DIR . $row['after_image']); }
        $db->prepare('DELETE FROM before_after WHERE id = ?')->execute([$id]);
        $flash = 'Item before-after dihapus.';
    }
}

$items = $db->query('SELECT ba.*, s.name AS service_name FROM before_after ba LEFT JOIN services s ON s.id = ba.service_id ORDER BY ba.id DESC')->fetchAll();
$services = $db->query('SELECT id, name FROM services ORDER BY id')->fetchAll();

$admin_title  = 'Kelola Before-After';
$admin_active = 'before-after';
require __DIR__ . '/includes/admin_header.php';
?>
<?php if ($flash): ?><div class="alert alert-info alert-dismissible"><?= e($flash) ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php endif; ?>

<p><button class="btn btn-warning" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus mr-1"></i>Tambah Before-After</button></p>

<div class="card"><div class="card-body table-responsive">
<table class="table table-bordered table-sm datatable">
  <thead><tr><th>Before</th><th>After</th><th>Judul</th><th>Layanan</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($items as $it): ?>
    <tr>
      <td style="width:90px"><img src="<?= img_url($it['before_image'], 'Before') ?>" alt="Before" style="width:80px;height:60px;object-fit:cover"></td>
      <td style="width:90px"><img src="<?= img_url($it['after_image'], 'After') ?>" alt="After" style="width:80px;height:60px;object-fit:cover"></td>
      <td><?= e($it['title']) ?></td>
      <td><?= e($it['service_name'] ?? '-') ?></td>
      <td class="text-nowrap">
        <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#editModal<?= (int)$it['id'] ?>"><i class="fas fa-edit"></i></button>
        <form method="post" class="d-inline confirm-form" data-title="Hapus item ini?" data-icon="warning">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
          <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
        </form>
      </td>
    </tr>
    <div class="modal fade" id="editModal<?= (int)$it['id'] ?>" tabindex="-1">
      <div class="modal-dialog">
        <form method="post" enctype="multipart/form-data" class="modal-content">
          <?= csrf_field() ?><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
          <div class="modal-header py-2"><h6 class="modal-title">Edit Before-After</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
          <div class="modal-body">
            <div class="form-group"><label class="small">Judul *</label><input type="text" name="title" class="form-control form-control-sm" value="<?= e($it['title']) ?>" required></div>
            <div class="form-group"><label class="small">Layanan Terkait</label>
              <select name="service_id" class="form-control form-control-sm">
                <option value="0">- Tidak ada -</option>
                <?php foreach ($services as $s): ?><option value="<?= (int)$s['id'] ?>" <?= (int)$it['service_id'] === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label class="small">Ganti Foto Before (opsional)</label><input type="file" name="before_image" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp"></div>
            <div class="form-group mb-0"><label class="small">Ganti Foto After (opsional)</label><input type="file" name="after_image" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp"></div>
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
      <div class="modal-header py-2"><h6 class="modal-title">Tambah Before-After</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group"><label class="small">Judul *</label><input type="text" name="title" class="form-control form-control-sm" required></div>
        <div class="form-group"><label class="small">Layanan Terkait</label>
          <select name="service_id" class="form-control form-control-sm">
            <option value="0">- Tidak ada -</option>
            <?php foreach ($services as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label class="small">Foto Before * (maks 2MB)</label><input type="file" name="before_image" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp" required></div>
        <div class="form-group mb-0"><label class="small">Foto After * (maks 2MB)</label><input type="file" name="after_image" class="form-control-file small" accept=".jpg,.jpeg,.png,.webp" required></div>
      </div>
      <div class="modal-footer py-2"><button class="btn btn-sm btn-warning">Tambah</button></div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
