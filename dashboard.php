<?php
// dashboard.php
require_once 'config/database.php';

if (!isLoggedIn()) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit();
    } else {
        redirect('login.php');
    }
}

// Handle AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    header('Content-Type: application/json');
    
    // Get user's shipments
    $stmt = $pdo->prepare("
        SELECT * FROM shipments 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Delivered' THEN 1 ELSE 0 END) as delivered,
            SUM(CASE WHEN status LIKE '%Transit%' OR status LIKE '%Delivery%' THEN 1 ELSE 0 END) as in_transit,
            SUM(CASE WHEN status = 'Processing' THEN 1 ELSE 0 END) as processing
        FROM shipments 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'shipments' => $shipments,
        'stats' => $stats
    ]);
    exit();
}

// Regular page view
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - DHL Express</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="animated-bg">
        <span></span><span></span><span></span><span></span><span></span>
    </div>

    <?php include 'includes/header.php'; ?>

    <section class="dashboard-page">
        <div class="container">
            <h1>My Dashboard</h1>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total Shipments</h3>
                    <p class="stat-number" id="total-shipments">0</p>
                </div>
                <div class="stat-card">
                    <h3>Delivered</h3>
                    <p class="stat-number" id="delivered-count">0</p>
                </div>
                <div class="stat-card">
                    <h3>In Transit</h3>
                    <p class="stat-number" id="transit-count">0</p>
                </div>
                <div class="stat-card">
                    <h3>Processing</h3>
                    <p class="stat-number" id="processing-count">0</p>
                </div>
            </div>
            
            <div class="shipments-list">
                <h2>Recent Shipments</h2>
                <table class="shipments-table" id="shipments-table">
                    <thead>
                        <tr>
                            <th>Tracking Number</th>
                            <th>Status</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="shipments-body">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
        });

        function loadDashboardData() {
            fetch('dashboard.php', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboardUI(data);
                }
            });
        }

        function updateDashboardUI(data) {
            // Update stats
            document.getElementById('total-shipments').textContent = data.stats.total || 0;
            document.getElementById('delivered-count').textContent = data.stats.delivered || 0;
            document.getElementById('transit-count').textContent = data.stats.in_transit || 0;
            document.getElementById('processing-count').textContent = data.stats.processing || 0;
            
            // Update shipments table
            const tbody = document.getElementById('shipments-body');
            tbody.innerHTML = '';
            
            if (data.shipments.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No shipments found</td></tr>';
            } else {
                data.shipments.forEach(shipment => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><strong>${shipment.tracking_number}</strong></td>
                        <td><span class="status-badge-small ${shipment.status === 'Delivered' ? 'delivered' : ''}">${shipment.status}</span></td>
                        <td>${shipment.origin}</td>
                        <td>${shipment.destination}</td>
                        <td>${new Date(shipment.created_at).toLocaleDateString()}</td>
                        <td><button class="track-small-btn" onclick="trackShipment('${shipment.tracking_number}')">Track</button></td>
                    `;
                    tbody.appendChild(row);
                });
            }
        }

        function trackShipment(trackingNumber) {
            window.location.href = `index.php?track=${trackingNumber}`;
        }
    </script>
</body>
</html>
