<?php
// Global session & security functions
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['message'] = 'Please login first!';
        header('Location: ../auth/login.php');
        exit();
    }
}

function humanTimeLeft($seconds) {
    if ($seconds < 60) return $seconds . 's';
    if ($seconds < 3600) return round($seconds/60) . 'm';
    if ($seconds < 86400) return round($seconds/3600) . 'h';
    return round($seconds/86400) . 'd';
}

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function displayMessage() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['error']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['error']);
    }
}

// Auto-include classes
spl_autoload_register(function ($class_name) {
    $class_file = "classes/" . $class_name . ".php";
    if (file_exists($class_file)) {
        require_once $class_file;
    }
});
?>