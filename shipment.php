<?php
// create_shipment.php
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to create a shipment']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$origin = sanitize($data['origin'] ?? '');
$destination = sanitize($data['destination'] ?? '');
$weight = sanitize($data['weight'] ?? '');

if (empty($origin) || empty($destination) || empty($weight)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
    exit();
}

// Generate unique tracking number
do {
    $tracking_number = 'DHL' . rand(100000, 999999);
    $stmt = $pdo->prepare("SELECT id FROM shipments WHERE tracking_number = ?");
    $stmt->execute([$tracking_number]);
} while ($stmt->fetch());

// Calculate estimated delivery (5 days from now)
$estimated_delivery = date('Y-m-d', strtotime('+5 days'));

// Insert shipment
$stmt = $pdo->prepare("
    INSERT INTO shipments (tracking_number, user_id, status, origin, destination, weight, estimated_delivery, current_location) 
    VALUES (?, ?, 'Processing', ?, ?, ?, ?, ?)
");

if ($stmt->execute([$tracking_number, $_SESSION['user_id'], $origin, $destination, $weight, $estimated_delivery, $origin])) {
    $shipment_id = $pdo->lastInsertId();
    
    // Add tracking history
    $stmt = $pdo->prepare("
        INSERT INTO tracking_history (shipment_id, location, status) 
        VALUES (?, ?, 'Shipment Created')
    ");
    $stmt->execute([$shipment_id, $origin]);
    
    echo json_encode([
        'success' => true,
        'tracking_number' => $tracking_number,
        'message' => 'Shipment created successfully!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create shipment']);
}
?>
