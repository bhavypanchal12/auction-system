<?php 
require_once '../config/config.php'; 

if (isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

$error = '';
if ($_POST) {
    $user = new User($pdo);
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    
    if ($user->login()) {
        header('Location: ../index.php');
        exit();
    } else {
        $error = 'Invalid credentials!';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - AuctionPHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">
                            <i class="fas fa-sign-in-alt text-primary me-2"></i>Login
                        </h3>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username or Email</label>
                                <input type="text" name="username" class="form-control" required 
                                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                        </form>
                        
                        <div class="text-center">
                            <p>Demo: <strong>seller1</strong> / <strong>password</strong></p>
                            <p>Don't have account? <a href="register.php">Register here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>