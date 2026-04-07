<?php 
require_once '../config/config.php'; 
requireLogin(); // From functions.php
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container-fluid py-5">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="#auctions" class="list-group-item list-group-item-action">
                        <i class="fas fa-gavel me-2"></i>Auctions
                    </a>
                    <a href="#users" class="list-group-item list-group-item-action">
                        <i class="fas fa-users me-2"></i>Users
                    </a>
                    <a href="#bids" class="list-group-item list-group-item-action">
                        <i class="fas fa-money-bill me-2"></i>Bids
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5>Total Auctions</h5>
                                <?php
                                $stmt = $pdo->query("SELECT COUNT(*) FROM auctions");
                                echo $stmt->fetchColumn();
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5>Active Auctions</h5>
                                <?php
                                $stmt = $pdo->query("SELECT COUNT(*) FROM auctions WHERE status='active'");
                                echo $stmt->fetchColumn();
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5>Total Users</h5>
                                <?php
                                $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                                echo $stmt->fetchColumn();
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5>Total Bids</h5>
                                <?php
                                $stmt = $pdo->query("SELECT COUNT(*) FROM bids");
                                echo $stmt->fetchColumn();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Auctions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-gavel me-2"></i>Recent Auctions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Current Price</th>
                                        <th>Status</th>
                                        <th>Seller</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT a.*, u.username FROM auctions a 
                                                       LEFT JOIN users u ON a.seller_id = u.id 
                                                       ORDER BY a.created_at DESC LIMIT 10");
                                    while ($row = $stmt->fetch()) {
                                        echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td>" . htmlspecialchars($row['title']) . "</td>
                                            <td>\${$row['current_price']}</td>
                                            <td>
                                                <span class='badge " . ($row['status']=='active' ? 'bg-success' : 'bg-secondary') . "'>
                                                    {$row['status']}
                                                </span>
                                            </td>
                                            <td>{$row['username']}</td>
                                            <td>
                                                <a href='../auctions/view.php?id={$row['id']}' class='btn btn-sm btn-primary'>View</a>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>