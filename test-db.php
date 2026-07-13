<?php
// FILE INI HANYA UNTUK DEBUG - HAPUS SETELAH SELESAI
$host = 'sql204.infinityfree.com';
$db   = 'if0_42105007_coatingcepat';
$user = 'if0_42105007';
$pass = 'Wuq1r9wkIjdfL';
$port = 3306;

echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "PDO drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n\n";
echo "Mencoba koneksi ke: $host...\n";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user, $pass,
        [
            PDO::ATTR_ERRMODE        => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => true,
            PDO::ATTR_TIMEOUT        => 15,
        ]
    );
    echo "✅ KONEKSI BERHASIL!\n";
    $r = $pdo->query("SHOW TABLES");
    echo "Tabel ditemukan:\n";
    while ($row = $r->fetch(PDO::FETCH_NUM)) {
        echo "  - " . $row[0] . "\n";
    }
} catch (PDOException $e) {
    echo "❌ GAGAL: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
}
echo "</pre>";
