<?php
// includes/header.php
?>
<header class="header">
    <div class="container">
        <div class="logo">
            <h1>DHL EXPRESS</h1>
        </div>
        <nav class="nav-menu">
            <ul>
                <li><a href="index.php" class="active">Track</a></li>
                <li><a href="#">Ship</a></li>
                <li><a href="#">Receive</a></li>
                <li><a href="#">Customer Support</a></li>
                <li>
                    <div id="auth-section">
                        <?php if (isLoggedIn()): ?>
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                <a href="dashboard.php" class="dashboard-btn">Dashboard</a>
                                <a href="logout.php" class="logout-btn">Logout</a>
                            </div>
                        <?php else: ?>
                            <div class="auth-buttons">
                                <a href="login.php" class="login-btn">Login</a>
                                <a href="register.php" class="register-btn">Register</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>
