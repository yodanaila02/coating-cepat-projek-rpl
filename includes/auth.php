<?php
/**
 * =====================================================
 * FITUR KEAMANAN — PROTEKSI HALAMAN ADMIN
 * Di-include di setiap halaman /admin (kecuali login).
 * Jika belum login -> redirect ke halaman login admin.
 * =====================================================
 */
require_once __DIR__ . '/functions.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

/* Data admin yang sedang login (untuk header AdminLTE) */
$admin_name  = $_SESSION['admin_name']  ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? '';
