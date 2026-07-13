<?php
/**
 * =====================================================
 * HEADER HALAMAN USER
 * Kompatibel InfinityFree
 * =====================================================
 */
$site_name  = setting($db, 'site_name', 'Coating Cepat');
$wa         = setting($db, 'whatsapp', '081279788675');
$_title     = isset($page_title) && $page_title !== ''
            ? $page_title . ' | ' . $site_name
            : setting($db, 'meta_title', $site_name);
$_desc      = isset($meta_desc) ? $meta_desc : setting($db, 'meta_description', '');
$_og_image  = isset($og_image) ? $og_image : (setting($db, 'logo') ? UPLOAD_URL . setting($db, 'logo') : BASE_URL . '/assets/img/og-default.jpg');
$_active    = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($_title) ?></title>
<meta name="description" content="<?= e($_desc) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($site_name) ?>">
<meta property="og:title" content="<?= e($_title) ?>">
<meta property="og:description" content="<?= e($_desc) ?>">
<meta property="og:image" content="<?= e($_og_image) ?>">
<meta property="og:url" content="<?= e(BASE_URL . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/')) ?>">
<link rel="icon" href="<?= setting($db,'logo') ? e(UPLOAD_URL . setting($db,'logo')) : 'data:,' ?>">

<!-- CDN (tanpa build process, siap shared hosting) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<!-- Path CSS pakai BASE_URL agar benar di root maupun subfolder -->
<link href="<?= e(BASE_URL) ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top main-navbar">
  <div class="container">
    <a class="navbar-brand heading-font" href="<?= e(BASE_URL) ?>/index.php?page=home">
      <?php if (setting($db, 'logo')): ?>
        <img src="<?= e(UPLOAD_URL . setting($db, 'logo')) ?>" alt="Logo <?= e($site_name) ?>" height="36" class="me-2">
      <?php else: ?>
        <i class="fa-solid fa-spray-can-sparkles text-accent me-2"></i>
      <?php endif; ?>
      <span class="text-uppercase"><?= e($site_name) ?></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <?php
        $menu = [
            'home' => 'Beranda', 'about' => 'Tentang', 'services' => 'Layanan',
            'gallery' => 'Galeri', 'before-after' => 'Before-After',
            'testimonials' => 'Testimoni', 'calculator' => 'Kalkulator',
            'tracking' => 'Tracking', 'faq' => 'FAQ', 'contact' => 'Kontak',
        ];
        foreach ($menu as $key => $label): ?>
          <li class="nav-item">
            <a class="nav-link <?= $_active === $key ? 'active' : '' ?>" href="<?= e(BASE_URL) ?>/index.php?page=<?= $key ?>"><?= e($label) ?></a>
          </li>
        <?php endforeach; ?>
        <li class="nav-item ms-lg-3 my-2 my-lg-0">
          <a class="btn btn-accent btn-sm px-3 text-uppercase" href="<?= e(BASE_URL) ?>/index.php?page=booking">Booking Sekarang</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="nav-spacer"></div>
