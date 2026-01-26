<?php
// Disable Xdebug output
putenv('XDEBUG_MODE=off');

try {
    // Connect to MySQL (no database specified)
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "[✓] MySQL connection successful\n";
    
    // Create database if it doesn't exist
    $pdo->exec('CREATE DATABASE IF NOT EXISTS stridesync2025');
    echo "[✓] Database 'stridesync2025' created or already exists\n";
    
    // Switch to the database
    $pdo->exec('USE stridesync2025');
    echo "[✓] Connected to database 'stridesync2025'\n";
    
    // List tables
    $result = $pdo->query('SHOW TABLES');
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "[✓] Tables in database: " . (count($tables) > 0 ? implode(', ', $tables) : 'NONE') . "\n";
    
} catch (PDOException $e) {
    echo "[✗] ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
