<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR ADMIN — KELOLA FAQ (CRUD)
 * Kategori (Ceramic Coating / Detailing / Booking),
 * pertanyaan, jawaban.
 * =====================================================
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$flash = '';
$cats = ['Ceramic Coating', 'Detailing', 'Booking'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'add' || $action === 'edit') {
        $cat = clean($_POST['category'] ?? '');
        $q   = clean($_POST['question'] ?? '');
        $a   = clean($_POST['answer'] ?? '');
        if (!in_array($cat, $cats, true) || $q === '' || $a === '') {
            $flash = 'Semua field wajib diisi dengan benar.';
        } elseif ($action === 'add') {
            $db->prepare('INSERT INTO faqs (category, question, answer) VALUES (?,?,?)')->execute([$cat, $q, $a]);
            $flash = 'FAQ ditambahkan.';
        } else {
            $db->prepare('UPDATE faqs SET category=?, question=?, answer=? WHERE id=?')->execute([$cat, $q, $a, $id]);
            $flash = 'FAQ diperbarui.';
        }
    } elseif ($action === 'delete') {
        $db->prepare('DELETE FROM faqs WHERE id = ?')->execute([$id]);
        $flash = 'FAQ dihapus.';
    }
}

$items = $db->query("SELECT * FROM faqs ORDER BY FIELD(category,'Ceramic Coating','Detailing','Booking'), id")->fetchAll();
$admin_title  = 'Kelola FAQ';
$admin_active = 'faqs';
require __DIR__ . '/includes/admin_header.php';
?>
<?php if ($flash): ?><div class="alert alert-info alert-dismissible"><?= e($flash) ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php endif; ?>

<p><button class="btn btn-warning" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus mr-1"></i>Tambah FAQ</button></p>

<div class="card"><div class="card-body table-responsive">
<table class="table table-bordered table-sm datatable">
  <thead><tr><th>Kategori</th><th>Pertanyaan</th><th>Jawaban</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($items as $f): ?>
    <tr>
      <td class="text-nowrap"><?= e($f['category']) ?></td>
      <td><?= e($f['question']) ?></td>
      <td class="small"><?= e(mb_strimwidth($f['answer'], 0, 110, '...')) ?></td>
      <td class="text-nowrap">
        <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#editModal<?= (int)$f['id'] ?>"><i class="fas fa-edit"></i></button>
        <form method="post" class="d-inline confirm-form" data-title="Hapus FAQ ini?" data-icon="warning">
          <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
          <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
        </form>
      </td>
    </tr>
    <div class="modal fade" id="editModal<?= (int)$f['id'] ?>" tabindex="-1">
      <div class="modal-dialog">
        <form method="post" class="modal-content">
          <?= csrf_field() ?><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
          <div class="modal-header py-2"><h6 class="modal-title">Edit FAQ</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
          <div class="modal-body">
            <div class="form-group"><label class="small">Kategori</label>
              <select name="category" class="form-control form-control-sm">
                <?php foreach ($cats as $c): ?><option value="<?= e($c) ?>" <?= $f['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label class="small">Pertanyaan *</label><input type="text" name="question" class="form-control form-control-sm" value="<?= e($f['question']) ?>" required></div>
            <div class="form-group mb-0"><label class="small">Jawaban *</label><textarea name="answer" class="form-control form-control-sm" rows="4" required><?= e($f['answer']) ?></textarea></div>
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
    <form method="post" class="modal-content">
      <?= csrf_field() ?><input type="hidden" name="action" value="add">
      <div class="modal-header py-2"><h6 class="modal-title">Tambah FAQ</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <div class="form-group"><label class="small">Kategori</label>
          <select name="category" class="form-control form-control-sm">
            <?php foreach ($cats as $c): ?><option value="<?= e($c) ?>"><?= e($c) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label class="small">Pertanyaan *</label><input type="text" name="question" class="form-control form-control-sm" required></div>
        <div class="form-group mb-0"><label class="small">Jawaban *</label><textarea name="answer" class="form-control form-control-sm" rows="4" required></textarea></div>
      </div>
      <div class="modal-footer py-2"><button class="btn btn-sm btn-warning">Tambah</button></div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
