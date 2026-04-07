<?php 
require_once '../config/config.php'; 
requireLogin();

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit();
}

$auction = new Auction($pdo);
$auction->id = $id;
if (!$auction->readOne()) {
    header('Location: index.php');
    exit();
}

// Get recent bids
$bids_stmt = $auction->getRecentBids(10);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($auction->title) ?> - AuctionPHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <!-- Auction Image -->
            <div class="col-lg-7 mb-4">
                <div class="position-relative overflow-hidden rounded-4 shadow-lg" style="height: 500px;">
                    <img src="<?= $auction->image ? '../assets/images/' . $auction->image : 'https://via.placeholder.com/800x500?text=Auction+Item' ?>" 
                         class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($auction->title) ?>">
                </div>
            </div>

            <!-- Auction Details & Bidding -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg h-100">
                    <div class="card-body p-4">
                        <h1 class="fw-bold text-primary mb-3"><?= htmlspecialchars($auction->title) ?></h1>
                        <p class="lead text-muted mb-4"><?= htmlspecialchars($auction->description) ?></p>
                        
                        <!-- Current Price -->
                        <div class="bg-gradient p-4 rounded-4 mb-4 text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <div class="h1 fw-bold mb-1" id="current-price">$<?= number_format($auction->current_price, 2) ?></div>
                            <div class="h6 mb-2">Current Bid</div>
                            <small>Seller: <strong><?= htmlspecialchars($auction->seller_name) ?></strong></small>
                        </div>

                        <!-- Time Left -->
                        <div class="alert alert-warning mb-4">
                            <h5><i class="fas fa-clock text-warning me-2"></i>Time Left</h5>
                            <div class="h3 fw-bold time-left" id="time-left">
                                Loading...
                            </div>
                        </div>

                        <!-- Bid Form -->
                        <?php if ($auction->status === 'active'): ?>
                        <form id="bid-form" class="mb-4">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-success text-white">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <input type="number" id="bid-amount" class="form-control" 
                                       placeholder="Enter your bid" step="0.01" min="<?= $auction->current_price + 1 ?>">
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-gavel me-2"></i>Place Bid
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Minimum bid: $<strong><?= number_format($auction->current_price + 1, 2) ?></strong>
                            </small>
                        </form>
                        <?php else: ?>
                        <div class="alert alert-secondary text-center">
                            <h4><i class="fas fa-flag-checkered"></i> Auction Ended</h4>
                        </div>
                        <?php endif; ?>

                        <!-- Quick Stats -->
                        <div class="row text-center mb-4">
                            <div class="col-4">
                                <div class="h5 fw-bold text-primary"><?= $auction->total_bids ?? 0 ?></div>
                                <small class="text-muted">Bids</small>
                            </div>
                            <div class="col-4">
                                <div class="h5 fw-bold text-success"><?= formatPrice($auction->starting_price) ?></div>
                                <small class="text-muted">Starting</small>
                            </div>
                            <div class="col-4">
                                <div class="h5 fw-bold text-info">
                                    <?= date('M j, Y H:i', strtotime($auction->end_time)) ?>
                                </div>
                                <small class="text-muted">Ends</small>
                            </div>
                        </div>

                        <hr>

                        <!-- Recent Bids -->
                        <h6><i class="fas fa-list me-2"></i>Recent Bids</h6>
                        <div class="recent-bids">
                            <?php if ($bids_stmt->rowCount()): ?>
                                <?php while ($bid = $bids_stmt->fetch()): ?>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span><i class="fas fa-user-circle text-muted me-2"></i><?= htmlspecialchars($bid['username']) ?></span>
                                    <span class="fw-bold text-success">$<?= number_format($bid['amount'], 2) ?></span>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <small class="text-muted">No bids yet</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    $(document).ready(function() {
        updateCountdown();
        setInterval(updateCountdown, 1000);
        
        // Bid form
        $('#bid-form').on('submit', function(e) {
            e.preventDefault();
            let amount = parseFloat($('#bid-amount').val());
            let currentPrice = <?= $auction->current_price ?>;
            
            if (amount > currentPrice) {
                $.post('bid.php', {auction_id: <?= $id ?>, amount: amount}, function(res) {
                    if (res.success) {
                        alert('✅ Bid placed!');
                        location.reload();
                    } else {
                        alert('❌ ' + res.message);
                    }
                }, 'json');
            } else {
                alert('Bid must be higher than current price!');
            }
        });
    });

    function updateCountdown() {
        let endTime = new Date('<?= $auction->end_time ?>').getTime();
        let now = new Date().getTime();
        let distance = endTime - now;
        
        if (distance < 0) {
            $('#time-left').html('<span class="text-danger">Auction Ended!</span>');
            return;
        }
        
        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        $('#time-left').html(`${days}d ${hours}h ${minutes}m ${seconds}s`);
    }
    </script>
</body>
</html>