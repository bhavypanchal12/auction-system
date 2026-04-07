<?php
// config/config.php - CORRECT PATHS FROM ROOT
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include functions (SAME LEVEL as config)
require_once __DIR__ . '/functions.php';

// Database (SAME LEVEL)
require_once 'database.php';

// Auto-load classes (ROOT level)
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Global database connection
try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>