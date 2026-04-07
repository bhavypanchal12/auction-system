<?php
require_once '../config/config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$auction_id = intval($_POST['auction_id']);
$amount = floatval($_POST['amount']);

$auction = new Auction($pdo);
$auction->id = $auction_id;

if (!$auction->readOne() || $auction->status !== 'active') {
    echo json_encode(['success' => false, 'message' => 'Auction not active']);
    exit;
}

if ($amount <= $auction->current_price) {
    echo json_encode(['success' => false, 'message' => 'Bid too low']);
    exit;
}

// Update balance & place bid
$user = new User($pdo);
$user->getById($_SESSION['user_id']);

if ($amount > $user->balance) {
    echo json_encode(['success' => false, 'message' => 'Insufficient balance']);
    exit;
}

$bid = new Bid($pdo);
$bid->auction_id = $auction_id;
$bid->user_id = $_SESSION['user_id'];
$bid->amount = $amount;

if ($bid->create()) {
    // Deduct balance
    $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?")
        ->execute([$amount, $_SESSION['user_id']]);
    
    $_SESSION['balance'] -= $amount;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Bid failed']);
}
?>