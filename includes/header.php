<?php include 'config/config.php'; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">
            <i class="fas fa-gavel"></i> AuctionPHP
        </a>
        
        <div class="navbar-nav ms-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="navbar-text me-3">
                    Hi, <?= htmlspecialchars($_SESSION['username']) ?> 
                    (Balance: $<?= number_format($_SESSION['balance'], 2) ?>)
                </span>
                <a class="nav-link" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="nav-link" href="login.php">Login</a>
                <a class="nav-link" href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>