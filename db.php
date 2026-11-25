<?php
/**
 * Database Connection using PDO
 * Alice House Hiriketiya - Accommodation Booking Management System
 */

// Database credentials for XAMPP
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'alice_house_db');
define('DB_PORT', 3306);

// Base application configuration
if (!defined('BASE_URL')) {
    // Dynamically detect base URL
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    // Check if in admin directory
    if (strpos($scriptDir, '/admin') !== false) {
        $baseUrl = dirname($scriptDir);
    } else {
        $baseUrl = $scriptDir;
    }
    $baseUrl = rtrim(str_replace('\\', '/', $baseUrl), '/');
    define('BASE_URL', $baseUrl);
}

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Provide a clear friendly error page if database is not created yet
            die("<div style='font-family:sans-serif; max-width:600px; margin:50px auto; padding:25px; border:2px solid #e53e3e; border-radius:12px; background:#fff5f5; color:#742a2a;'>
                <h2 style='margin-top:0;'>⚠️ Database Connection Failed</h2>
                <p>Could not connect to MySQL database <strong>" . DB_NAME . "</strong>.</p>
                <p><strong>Troubleshooting Steps for XAMPP:</strong></p>
                <ol>
                    <li>Make sure <strong>Apache</strong> and <strong>MySQL</strong> are started in the XAMPP Control Panel.</li>
                    <li>Open <code>http://localhost/phpmyadmin/</code></li>
                    <li>Create a database named <code>" . DB_NAME . "</code> and import <code>db_structure.sql</code>.</li>
                </ol>
                <p style='font-size:13px; color:#a0aec0;'>Error details: " . htmlspecialchars($e->getMessage()) . "</p>
            </div>");
        }
    }
    return $pdo;
}
