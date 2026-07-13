<?php
ob_start();
error_reporting(0);
ini_set("display_errors", 0);
/**
 * =====================================================
 * FITUR ADMIN — LOGIN
 * - Email + password (password_verify terhadap password_hash)
 * - Proteksi brute force: maks 5 percobaan gagal per IP,
 *   diblokir 15 menit (tabel login_attempts)
 * - session_regenerate_id() setelah login sukses
 * - CSRF token
 * =====================================================
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

/* Sudah login? langsung ke dashboard */
if (!empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$error = '';
$MAX_ATTEMPTS = 5;       // maksimal percobaan gagal
$LOCK_MINUTES = 15;      // durasi blokir (menit)
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    /* --- Cek blokir brute force --- */
    $st = $db->prepare('SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?');
    $st->execute([$ip]);
    $attempt = $st->fetch();
    $locked = false;
    if ($attempt && (int)$attempt['attempts'] >= $MAX_ATTEMPTS) {
        $elapsed = time() - strtotime($attempt['last_attempt']);
        if ($elapsed < $LOCK_MINUTES * 60) {
            $locked = true;
            $sisa = ceil(($LOCK_MINUTES * 60 - $elapsed) / 60);
            $error = "Terlalu banyak percobaan gagal. Coba lagi dalam {$sisa} menit.";
        } else {
            // masa blokir habis -> reset hitungan
            $db->prepare('DELETE FROM login_attempts WHERE ip_address = ?')->execute([$ip]);
            $attempt = null;
        }
    }

    if (!$locked) {
        $email    = clean($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        $st = $db->prepare('SELECT * FROM admins WHERE email = ?');
        $st->execute([$email]);
        $admin = $st->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            /* --- Login sukses --- */
            $db->prepare('DELETE FROM login_attempts WHERE ip_address = ?')->execute([$ip]); // reset attempt
            session_regenerate_id(true); // FITUR KEAMANAN: cegah session fixation
            $_SESSION['admin_id']    = (int)$admin['id'];
            $_SESSION['admin_name']  = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            redirect('dashboard.php');
        }

        /* --- Login gagal: catat percobaan --- */
        if ($attempt) {
            $db->prepare('UPDATE login_attempts SET attempts = attempts + 1, last_attempt = NOW() WHERE ip_address = ?')->execute([$ip]);
        } else {
            $db->prepare('INSERT INTO login_attempts (ip_address, attempts, last_attempt) VALUES (?, 1, NOW())')->execute([$ip]);
        }
        $error = 'Email atau password salah.';
        usleep(500000); // delay 0.5 detik memperlambat brute force
    }
}
$site_name = setting($db, 'site_name', 'Coating Cepat');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Login Admin | <?= e($site_name) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<style>
  body.login-page { background: #0D0D0D; }
  .login-logo a { color: #D98E32 !important; font-weight: 700; letter-spacing: .08em; }
  .card { background: #1A1A1A; border: 1px solid #2B2B2B; }
  .login-card-body { background: #1A1A1A; color: #eee; }
  .form-control { background: #0D0D0D; border-color: #2B2B2B; color: #eee; }
  .form-control:focus { background:#0D0D0D; color:#eee; border-color:#D98E32; box-shadow:none; }
  .btn-accent { background: #D98E32; border-color: #D98E32; color: #0D0D0D; font-weight: 600; }
  .input-group-text { background:#0D0D0D; border-color:#2B2B2B; color:#D98E32; }
</style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="../index.php?page=home"><i class="fas fa-spray-can-sparkles mr-2"></i><?= e(strtoupper($site_name)) ?></a>
  </div>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Masuk ke Admin Panel</p>

      <?php if ($error): ?>
        <div class="alert alert-danger py-2 small"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <?= csrf_field() ?>
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <button type="submit" class="btn btn-accent btn-block">Login</button>
      </form>

      <p class="mt-3 mb-0 text-center"><a href="../index.php?page=home" class="text-muted small">&larr; Kembali ke website</a></p>
    </div>
  </div>
</div>
</body>
</html>
