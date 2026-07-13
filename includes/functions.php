<?php
/**
 * =====================================================
 * HELPER FUNCTIONS
 * Berisi: escape XSS, CSRF token, format rupiah,
 * upload gambar aman, pengaturan website, kode booking,
 * link WhatsApp, badge status.
 * =====================================================
 */

// Konfigurasi session yang kompatibel InfinityFree
if (session_status() === PHP_SESSION_NONE) {
    // InfinityFree: gunakan session_save_path default, tidak set cookie samesite via array
    // karena PHP 7.x di InfinityFree belum mendukung parameter array
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 1 : 0);
    session_start();
}

/* ---------- FITUR KEAMANAN: XSS protection (escape semua output) ---------- */
function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/* ---------- FITUR KEAMANAN: CSRF token (semua form POST) ---------- */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}
function csrf_verify() {
    $token = $_POST['csrf_token'] ?? '';
    $expected = $_SESSION['csrf_token'] ?? '';
    if (empty($token) || !hash_equals($expected, $token)) {
        http_response_code(403);
        die('Token keamanan (CSRF) tidak valid. Silakan muat ulang halaman.');
    }
}

/* ---------- FORMAT RUPIAH ---------- */
function format_rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/* ---------- SANITASI INPUT ---------- */
function clean($str) {
    return trim((string)$str);
}

/* ---------- AMBIL PENGATURAN WEBSITE (tabel settings) ---------- */
function get_settings($db) {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            foreach ($db->query('SELECT setting_key, setting_value FROM settings') as $row) {
                $cache[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            // Tabel settings belum ada / belum diimport
            $cache = [];
        }
    }
    return $cache;
}
function setting($db, $key, $default = '') {
    $s = get_settings($db);
    return isset($s[$key]) && $s[$key] !== '' ? $s[$key] : $default;
}

/* ---------- NOMOR WA -> FORMAT wa.me (62xxx) ---------- */
function wa_number($phone) {
    $p = preg_replace('/\D/', '', (string)$phone);
    if (strpos($p, '0') === 0) { $p = '62' . substr($p, 1); }
    return $p;
}
function wa_link($phone, $text = '') {
    return 'https://wa.me/' . wa_number($phone) . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}

/* ---------- VALIDASI NOMOR WA (format 08xx / 628xx, 10-15 digit) ---------- */
function valid_phone($phone) {
    $p = preg_replace('/\D/', '', (string)$phone);
    return (bool)preg_match('/^(08|628)\d+$/', $p) && strlen($p) >= 10 && strlen($p) <= 15;
}

/* ---------- FITUR BOOKING: kode booking unik CC-YYYYMMDD-XXXX ---------- */
function generate_booking_code($db) {
    do {
        $code = 'CC-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        $st = $db->prepare('SELECT COUNT(*) FROM bookings WHERE booking_code = ?');
        $st->execute([$code]);
    } while ($st->fetchColumn() > 0);
    return $code;
}

/* ---------- FITUR KEAMANAN: upload gambar aman ----------
 * - Whitelist ekstensi: jpg, jpeg, png, webp
 * - Cek MIME asli file (bukan dari nama file)
 * - Maksimal 2MB (limit InfinityFree)
 * - Rename acak (file PHP otomatis tertolak)
 * Return: nama file baru, atau null jika gagal/kosong.
 */
function upload_image($field, &$error = null) {
    if (empty($_FILES[$field]['name'])) { return null; }
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) { $error = 'Upload gagal (kode ' . $f['error'] . ').'; return null; }
    if ($f['size'] > MAX_UPLOAD_SIZE)  { $error = 'Ukuran file maksimal 2MB.'; return null; }

    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $allowed_ext  = ['jpg', 'jpeg', 'png', 'webp'];
    $allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($ext, $allowed_ext, true)) { $error = 'Hanya file JPG, PNG, atau WEBP yang diizinkan.'; return null; }

    // Deteksi MIME type
    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);
    }
    if (empty($mime)) {
        // Fallback: getimagesize
        $info = @getimagesize($f['tmp_name']);
        $mime = $info['mime'] ?? '';
    }
    if (!in_array($mime, $allowed_mime, true)) { $error = 'File bukan gambar yang valid.'; return null; }

    // Pastikan folder uploads ada dan bisa ditulis
    if (!is_dir(UPLOAD_DIR)) {
        @mkdir(UPLOAD_DIR, 0755, true);
    }
    if (!is_writable(UPLOAD_DIR)) {
        $error = 'Folder /uploads tidak dapat ditulis. Set permission ke 755.';
        return null;
    }

    $newname = date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . $newname)) {
        $error = 'Gagal menyimpan file. Pastikan folder /uploads dapat ditulis (permission 755).';
        return null;
    }
    return $newname;
}

/* ---------- URL GAMBAR (fallback placeholder jika file belum ada) ---------- */
function img_url($filename, $label = 'Coating Cepat') {
    if ($filename && file_exists(UPLOAD_DIR . $filename)) {
        return UPLOAD_URL . rawurlencode($filename);
    }
    // Placeholder SVG gelap untuk data seed sebelum foto asli di-upload
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600">'
         . '<rect width="100%" height="100%" fill="#1A1A1A"/>'
         . '<rect x="1" y="1" width="798" height="598" fill="none" stroke="#2B2B2B" stroke-width="2"/>'
         . '<text x="50%" y="48%" fill="#D98E32" font-family="Arial" font-size="34" font-weight="bold" text-anchor="middle">' . htmlspecialchars($label) . '</text>'
         . '<text x="50%" y="58%" fill="#777" font-family="Arial" font-size="18" text-anchor="middle">Upload foto asli via Admin Panel</text></svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

/* ---------- REDIRECT ---------- */
function redirect($url) {
    // Pastikan tidak ada output sebelum header (InfinityFree strict)
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }
    // Fallback: meta refresh jika header sudah terkirim
    echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url, ENT_QUOTES) . '">';
    echo '<script>window.location.href=' . json_encode($url) . ';</script>';
    exit;
}

/* ---------- FITUR BOOKING: badge status ---------- */
function status_badge($status) {
    $map = [
        'pending'     => ['secondary', 'Menunggu Konfirmasi'],
        'confirmed'   => ['primary',   'Dikonfirmasi'],
        'rescheduled' => ['warning text-dark', 'Dijadwal Ulang'],
        'done'        => ['success',   'Selesai'],
        'rejected'    => ['danger',    'Ditolak'],
    ];
    $info = $map[$status] ?? ['secondary', $status];
    return '<span class="badge bg-' . $info[0] . '">' . e($info[1]) . '</span>';
}
