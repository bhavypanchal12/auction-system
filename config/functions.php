
<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Please login first!';
        header('Location: auth/login.php');
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
?>