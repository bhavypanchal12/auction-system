<?php
require_once '../config/config.php';
requireLogin();

$id = $_GET['id'];
$pdo->prepare("DELETE FROM auctions WHERE id = ?")->execute([$id]);
$_SESSION['message'] = 'Auction deleted!';
header('Location: dashboard.php');
?>