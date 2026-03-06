<?php
// index.php
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DHL Express Clone - Track Your Shipment</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg">
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
    </div>

    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Track Your Shipment</h1>
            <p>Enter your tracking number to get real-time updates</p>
            
            <div class="tracking-form">
                <input type="text" id="tracking-number" placeholder="Enter tracking number (e.g., DHL123456)" class="tracking-input">
                <button id="track-btn" class="track-btn">Track Package</button>
            </div>
            
            <div class="quick-links">
                <span>Popular tracking numbers: </span>
                <button class="quick-link" data-tracking="DHL123456">DHL123456</button>
                <button class="quick-link" data-tracking="DHL789012">DHL789012</button>
                <button class="quick-link" data-tracking="DHL345678">DHL345678</button>
            </div>
        </div>
    </section>

    <!-- Create Shipment Section (Visible only when logged in) -->
    <?php if (isLoggedIn()): ?>
    <section class="create-shipment" id="create-shipment-section">
        <div class="container">
            <h2>Create New Shipment</h2>
            <div class="shipment-form">
                <input type="text" id="origin" placeholder="Origin (e.g., New York, USA)" class="form-input">
                <input type="text" id="destination" placeholder="Destination (e.g., London, UK)" class="form-input">
                <input type="text" id="weight" placeholder="Weight (kg)" class="form-input">
                <button id="create-shipment-btn" class="create-btn">Create Shipment</button>
            </div>
        </div>
    </section>

    <!-- User Dashboard -->
    <section class="user-dashboard" id="user-dashboard">
        <div class="container">
            <h2 style="color: white; text-align: center; margin-bottom: 30px;">My Shipments Dashboard</h2>
            <div class="dashboard-grid" id="dashboard-content">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Tracking Results -->
    <section class="tracking-results" id="tracking-results" style="display: none;">
        <div class="container">
            <div class="result-card">
                <div class="result-header">
                    <h2>Tracking Results: <span id="display-tracking-number"></span></h2>
                    <span class="status-badge" id="shipment-status">In Transit</span>
                </div>
                
                <div class="shipment-info">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Origin</label>
                            <p id="origin-display"></p>
                        </div>
                        <div class="info-item">
                            <label>Destination</label>
                            <p id="destination-display"></p>
                        </div>
                        <div class="info-item">
                            <label>Current Location</label>
                            <p id="current-location"></p>
                        </div>
                        <div class="info-item">
                            <label>Est. Delivery</label>
                            <p id="estimated-delivery"></p>
                        </div>
                        <div class="info-item">
                            <label>Last Update</label>
                            <p id="last-update"></p>
                        </div>
                    </div>
                </div>
                
                <div class="tracking-history">
                    <h3>Tracking History</h3>
                    <div class="timeline" id="tracking-timeline"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2>Why Choose DHL Express?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🚚</div>
                    <h3>Fast Delivery</h3>
                    <p>Time-definite delivery to over 220 countries and territories</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Secure Shipping</h3>
                    <p>Your packages are handled with utmost care and security</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📍</div>
                    <h3>Real-time Tracking</h3>
                    <p>Track your shipment every step of the way</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌍</div>
                    <h3>Global Network</h3>
                    <p>World's most international network</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>
