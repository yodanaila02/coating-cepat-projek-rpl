<?php
/**
 * =====================================================
 * KONFIGURASI UTAMA WEBSITE "COATING CEPAT"
 * Sudah dikonfigurasi untuk akun InfinityFree kamu
 * =====================================================
 */

// ===== KONFIGURASI DATABASE =====
define('DB_HOST', 'sql204.infinityfree.com');
define('DB_NAME', 'if0_42105007_coatingcepat');
define('DB_USER', 'if0_42105007');
define('DB_PASS', 'Wuq1r9wkIjdfL');

// ===== ZONA WAKTU =====
date_default_timezone_set('Asia/Jakarta');

// ===== BASE_URL OTOMATIS =====
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$dir = dirname($scriptName);
if (substr($dir, -6) === '/admin') {
    $dir = substr($dir, 0, -6);
}
$dir = rtrim($dir, '/');

define('BASE_URL', $scheme . '://' . $host . $dir);

// ===== PATH UPLOAD =====
$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$baseDir = $docRoot . ($dir !== '' ? $dir : '');
define('UPLOAD_DIR',      $baseDir . '/uploads/');
define('UPLOAD_URL',      BASE_URL . '/uploads/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2MB
