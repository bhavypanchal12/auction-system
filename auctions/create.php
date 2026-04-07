<?php 
include '../config/config.php'; 
include '../classes/Auction.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    $auction = new Auction($db);

    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "../assets/images/";
        $image = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
        $image = str_replace('../', '', $image);
    }

    $auction->title = $_POST['title'];
    $auction->description = $_POST['description'];
    $auction->image = $image;
    $auction->starting_price = $_POST['starting_price'];
    $auction->end_time = $_POST['end_time'];
    $auction->seller_id = $_SESSION['user_id'];

    if ($auction->create()) {
        $_SESSION['message'] = 'Auction created successfully!';
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Auction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container my-5">
        <h2>Create New Auction</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Starting Price</label>
                <input type="number" step="0.01" class="form-control" name="starting_price" required>
            </div>
            <div class="mb-3">
                <label class="form-label">End Time</label>
                <input type="datetime-local" class="form-control" name="end_time" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Image (Optional)</label>
                <input type="file" class="form-control" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Create Auction</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <?php 
        // In create.php form handler, after $auction->create():
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../assets/images/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $image_name = time() . '_' . basename($_FILES['image']['name']);
    $image_path = $upload_dir . $image_name;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
        $auction->image = 'assets/images/' . $image_name;
        // Update auction with image path
        $pdo->prepare("UPDATE auctions SET image = ? WHERE id = LAST_INSERT_ID()")
            ->execute([$auction->image]);
    }
}
    ?>
</body>
</html>