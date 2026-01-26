<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->exec('DROP DATABASE IF EXISTS stridesync2025');
    echo "[✓] Database dropped\n";
} catch (Exception $e) {
    echo "[✗] " . $e->getMessage() . "\n";
}
?>
