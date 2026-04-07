<?php
include '../config/config.php';
include '../classes/Auction.php';

header('Content-Type: text/html; charset=utf-8');

$database = new Database();
$db = $database->getConnection();
$auction = new Auction($db);
$stmt = $auction->readActive();

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $timeLeft = strtotime($row['end_time']) - time();
        $timeDisplay = $timeLeft > 0 ? humanTimeLeft($timeLeft) : '<span class="text-danger">Ended</span>';
        
        echo '
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 auction-card" data-id="' . $row['id'] . '">
                <img src="' . ($row['image'] ? '../assets/images/' . $row['image'] : 'https://via.placeholder.com/400x250?text=' . urlencode($row['title'])) . '" 
                     class="card-img-top" style="height: 250px; object-fit: cover;" alt="' . htmlspecialchars($row['title']) . '">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold">' . htmlspecialchars($row['title']) . '</h5>
                    <p class="card-text text-muted flex-grow-1">' . substr(htmlspecialchars($row['description'] ?? ''), 0, 100) . '...</p>
                    
                    <div class="mt-auto">
                        <div class="h3 text-success mb-2 fw-bold" id="current-price-' . $row['id'] . '">
                            $' . number_format($row['current_price'], 2) . '
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">
                                <i class="fas fa-clock me-1"></i>' . $timeDisplay . '
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-gavel me-1"></i>' . $row['bid_count'] . ' bids
                            </small>
                            <small class="text-muted d-block">
                                By <strong>' . htmlspecialchars($row['seller_name']) . '</strong>
                            </small>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="view.php?id=' . $row['id'] . '" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i>View Auction
                            </a>
                            ' . (isset($_SESSION['user_id']) ? '
                            <button class="btn btn-success bid-btn" data-auction-id="' . $row['id'] . '">
                                <i class="fas fa-gavel me-1"></i>Quick Bid
                            </button>' : '') . '
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
} else {
    echo '<div class="col-12"><div class="alert alert-info text-center">No active auctions found. <a href="create.php">Create one now!</a></div></div>';
}

function humanTimeLeft($seconds) {
    if ($seconds < 60) return $seconds . 's';
    elseif ($seconds < 3600) return round($seconds/60) . 'm';
    elseif ($seconds < 86400) return round($seconds/3600) . 'h';
    else return round($seconds/86400) . 'd';
}

require_once '../config/config.php';
$auction = new Auction($pdo);
$stmt = $auction->readActive();

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $timeLeft = strtotime($row['end_time']) - time();
        echo "
        <div class='col-md-4 mb-4'>
            <div class='card auction-card shadow-lg h-100' data-id='{$row['id']}'>
                <img src='{$row['image'] ?: 'https://via.placeholder.com/400x250?text=" . urlencode($row['title']) . "'}' 
                     class='card-img-top' style='height:250px;object-fit:cover'>
                <div class='card-body'>
                    <h5 class='card-title fw-bold'>{$row['title']}</h5>
                    <p class='card-text text-muted'>" . substr($row['description'], 0, 80) . "...</p>
                    <div class='h3 text-success fw-bold mb-2' id='current-price-{$row['id']}'>
                        $" . number_format($row['current_price'], 2) . "
                    </div>
                    <small class='text-muted'>
                        <i class='fas fa-clock me-1'></i>" . humanTimeLeft($timeLeft) . " | 
                        {$row['bid_count']} bids | By {$row['seller_name']}
                    </small>
                    <div class='mt-3'>
                        <a href='view.php?id={$row['id']}' class='btn btn-primary w-100 mb-2'>View Auction</a>
                        <button class='btn btn-success w-100 bid-btn' data-auction-id='{$row['id']}'>
                            <i class='fas fa-gavel'></i> Quick Bid
                        </button>
                    </div>
                </div>
            </div>
        </div>";
    }
} else {
    echo "<div class='col-12'><div class='alert alert-info text-center h4'>No auctions yet. <a href='create.php'>Create one!</a></div></div>";
}

?>