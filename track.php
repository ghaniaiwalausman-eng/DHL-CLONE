<?php
// track.php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$tracking_number = strtoupper(sanitize($data['tracking_number'] ?? ''));

if (empty($tracking_number)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a tracking number']);
    exit();
}

// Check if shipment exists
$stmt = $pdo->prepare("
    SELECT s.*, 
           (SELECT COUNT(*) FROM tracking_history WHERE shipment_id = s.id) as history_count
    FROM shipments s 
    WHERE s.tracking_number = ?
");
$stmt->execute([$tracking_number]);
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($shipment) {
    // Get tracking history
    $stmt = $pdo->prepare("
        SELECT date, location, status 
        FROM tracking_history 
        WHERE shipment_id = ? 
        ORDER BY date DESC
    ");
    $stmt->execute([$shipment['id']]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'status' => $shipment['status'],
            'origin' => $shipment['origin'],
            'destination' => $shipment['destination'],
            'current_location' => $shipment['current_location'] ?? $shipment['origin'],
            'estimated_delivery' => $shipment['estimated_delivery'],
            'last_update' => $shipment['last_update'],
            'tracking_history' => $history
        ],
        'tracking_number' => $tracking_number
    ]);
} else {
    // Generate random data for demo
    $statuses = ["In Transit", "Processing", "Out for Delivery", "Customs Clearance"];
    $cities = ["New York", "London", "Tokyo", "Paris", "Berlin", "Dubai", "Singapore"];
    $countries = ["USA", "UK", "Japan", "France", "Germany", "UAE", "Singapore"];
    
    $origin_city = $cities[array_rand($cities)];
    $dest_city = $cities[array_rand(array_diff($cities, [$origin_city]))];
    
    $today = date('Y-m-d H:i:s');
    $est_delivery = date('Y-m-d', strtotime('+' . rand(1, 7) . ' days'));
    
    $history = [];
    for ($i = 1; $i <= 3; $i++) {
        $history[] = [
            'date' => date('Y-m-d H:i:s', strtotime("-$i days")),
            'location' => $cities[array_rand($cities)] . ', ' . $countries[array_rand($countries)],
            'status' => ['Departed', 'Processed', 'In Transit', 'Arrived'][array_rand(['Departed', 'Processed', 'In Transit', 'Arrived'])]
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'status' => $statuses[array_rand($statuses)],
            'origin' => $origin_city . ', ' . $countries[array_rand($countries)],
            'destination' => $dest_city . ', ' . $countries[array_rand($countries)],
            'current_location' => $cities[array_rand($cities)] . ', ' . $countries[array_rand($countries)],
            'estimated_delivery' => $est_delivery,
            'last_update' => $today,
            'tracking_history' => $history
        ],
        'tracking_number' => $tracking_number
    ]);
}
?>
