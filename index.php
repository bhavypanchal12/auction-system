<?php include 'config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Auction - Live Bidding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-4 fw-bold"><i class="fas fa-gavel text-primary"></i> Live Auctions</h1>
            </div>
            <div class="col-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="auctions/create.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i> Create Auction
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row" id="auctions-container">
            <!-- Auctions loaded via AJAX -->
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="assets/js/auctions.js"></script>

    
</body>
</html>
