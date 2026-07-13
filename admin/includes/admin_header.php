<?php
/**
 * =====================================================
 * HEADER ADMIN PANEL (AdminLTE 3 via CDN)
 * Kompatibel InfinityFree
 * =====================================================
 */
ob_start();
$site_name    = setting($db, 'site_name', 'Coating Cepat');
$admin_title  = isset($admin_title)  ? $admin_title  : 'Dashboard';
$admin_active = isset($admin_active) ? $admin_active : 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($admin_title) ?> | Admin <?= e($site_name) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
<style>
  .brand-link .brand-text { font-weight: 700; }
  .accent { color: #D98E32 !important; }
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar atas -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><a href="../index.php?page=home" target="_blank" class="nav-link">Lihat Website</a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><span class="nav-link"><i class="fas fa-user-circle mr-1"></i><?= e($admin_name) ?></span></li>
      <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a></li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-warning elevation-4">
    <a href="dashboard.php" class="brand-link">
      <i class="fas fa-spray-can-sparkles ml-2 mr-2 accent"></i>
      <span class="brand-text accent"><?= e(strtoupper($site_name)) ?></span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <?php
          $menus = [
            'dashboard'    => ['dashboard.php',    'fa-tachometer-alt', 'Dashboard'],
            'bookings'     => ['bookings.php',     'fa-calendar-check', 'Kelola Booking'],
            'services'     => ['services.php',     'fa-spray-can',      'Kelola Layanan'],
            'gallery'      => ['gallery.php',      'fa-images',         'Kelola Galeri'],
            'before-after' => ['before-after.php', 'fa-exchange-alt',   'Kelola Before-After'],
            'testimonials' => ['testimonials.php', 'fa-comment-dots',   'Kelola Testimoni'],
            'faqs'         => ['faqs.php',         'fa-question-circle','Kelola FAQ'],
            'schedule'     => ['schedule.php',     'fa-calendar-times', 'Kelola Jadwal'],
            'settings'     => ['settings.php',     'fa-cog',            'Pengaturan Website'],
          ];
          foreach ($menus as $key => $m): ?>
          <li class="nav-item">
            <a href="<?= $m[0] ?>" class="nav-link <?= $admin_active === $key ? 'active' : '' ?>">
              <i class="nav-icon fas <?= $m[1] ?>"></i><p><?= $m[2] ?></p>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Konten utama -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1><?= e($admin_title) ?></h1>
      </div>
    </section>
    <section class="content"><div class="container-fluid">
