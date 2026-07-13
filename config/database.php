<?php
/**
 * =====================================================
 * KONEKSI DATABASE (PDO)
 * Disesuaikan untuk InfinityFree
 * =====================================================
 */
require_once __DIR__ . '/config.php';

// InfinityFree kadang butuh beberapa detik warm-up, retry 3x
$maxRetry = 3;
$attempt  = 0;
$db       = null;

while ($attempt < $maxRetry) {
    try {
        $db = new PDO(
            'mysql:host=' . DB_HOST . ';port=3306;dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => true,
                PDO::ATTR_PERSISTENT         => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                PDO::ATTR_TIMEOUT            => 15,
            ]
        );
        break; // sukses, keluar loop
    } catch (PDOException $e) {
        $attempt++;
        $lastError = $e->getMessage();
        if ($attempt < $maxRetry) {
            sleep(1); // tunggu 1 detik sebelum retry
        }
    }
}

if ($db === null) {
    http_response_code(500);
    // Tampilkan error detail untuk membantu debug
    $safeError = htmlspecialchars($lastError ?? 'Unknown error');
    die('
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Error Koneksi</title>
<style>
body{font-family:sans-serif;background:#0D0D0D;color:#eee;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
.box{background:#1A1A1A;border:1px solid #2B2B2B;padding:2rem;border-radius:8px;max-width:600px;text-align:center;}
h2{color:#D98E32;}p{color:#aaa;font-size:.9rem;}
.err{background:#2a0000;border:1px solid #700;padding:1rem;border-radius:6px;margin-top:1rem;font-family:monospace;font-size:.85rem;color:#f88;text-align:left;word-break:break-all;}
</style></head>
<body><div class="box">
<h2>&#9888; Gagal Terhubung ke Database</h2>
<p>Konfigurasi di <code>config/config.php</code>:<br><br>
<b>HOST:</b> ' . DB_HOST . '<br>
<b>DB:</b> ' . DB_NAME . '<br>
<b>USER:</b> ' . DB_USER . '
</p>
<div class="err"><b>Error:</b><br>' . $safeError . '</div>
</div></body></html>
    ');
}
