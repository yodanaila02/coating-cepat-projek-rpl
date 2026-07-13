<?php
/**
 * =====================================================
 * ENTRY POINT + ROUTER WEBSITE "COATING CEPAT"
 * Kompatibel InfinityFree
 * =====================================================
 */

// Buffer output agar header() tidak gagal di InfinityFree
ob_start();

// Sembunyikan error di produksi (InfinityFree tidak suka error output)
// Aktifkan baris bawah ini saat DEBUG saja, matikan saat produksi:
// ini_set('display_errors', 1); error_reporting(E_ALL);
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

/* Whitelist halaman (mencegah Local File Inclusion) */
$routes = [
    'home'            => 'pages/home.php',
    'about'           => 'pages/about.php',
    'services'        => 'pages/services.php',
    'service-detail'  => 'pages/service-detail.php',
    'gallery'         => 'pages/gallery.php',
    'before-after'    => 'pages/before-after.php',
    'testimonials'    => 'pages/testimonials.php',
    'calculator'      => 'pages/calculator.php',
    'booking'         => 'pages/booking.php',
    'booking-success' => 'pages/booking-success.php',
    'tracking'        => 'pages/tracking.php',
    'faq'             => 'pages/faq.php',
    'contact'         => 'pages/contact.php',
];

/* Default = home */
$page = isset($_GET['page']) ? (string)$_GET['page'] : 'home';

if (!isset($routes[$page])) {
    http_response_code(404);
    $page_title = 'Halaman Tidak Ditemukan';
    require __DIR__ . '/includes/header.php';
    echo '<div class="container py-5 text-center" style="min-height:60vh">
            <h1 class="display-1 text-accent heading-font mt-5">404</h1>
            <p class="lead text-light">Halaman yang Anda cari tidak ditemukan.</p>
            <a href="index.php?page=home" class="btn btn-accent mt-3">Kembali ke Beranda</a>
          </div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

require __DIR__ . '/' . $routes[$page];
