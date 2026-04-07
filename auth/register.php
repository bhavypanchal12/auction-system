<?php 
require_once '../config/config.php'; 

if ($_POST) {
    $user = new User($pdo);
    $user->username = $_POST['username'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    
    if ($user->create()) {
        $_SESSION['message'] = 'Account created! Please login.';
        header('Location: login.php');
        exit();
    } else {
        $error = 'Registration failed! Username/Email may exist.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - AuctionPHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">
                            <i class="fas fa-user-plus text-success me-2"></i>Register
                        </h3>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-success w-100 mb-3">Register</button>
                        </form>
                        <div class="text-center">
                            <p>Already have account? <a href="login.php">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>