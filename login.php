<?php
// login.php
require_once 'config/database.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            redirect('index.php');
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DHL Express</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="animated-bg">
        <span></span><span></span><span></span><span></span><span></span>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <h2>Login to DHL Express</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="email" name="email" class="form-input" placeholder="Email Address" required>
                <input type="password" name="password" class="form-input" placeholder="Password" required>
                <button type="submit" class="auth-btn">Login</button>
            </form>
            
            <p class="text-center">Don't have an account? <a href="register.php" class="auth-link">Register here</a></p>
            <p class="text-center"><a href="index.php" class="auth-link">Back to Home</a></p>
        </div>
    </div>
</body>
</html>
