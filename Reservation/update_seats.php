<?php
// Include database connection
require_once '../includes/db_connect.php';
session_start();

// Get data from POST request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['event_id']) || !isset($data['selected_seats']) || empty($data['selected_seats'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$eventId = filter_var($data['event_id'], FILTER_SANITIZE_NUMBER_INT);
$selectedSeats = $data['selected_seats'];

// Check if user is logged in (uncomment if you have user authentication)

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}
$userId = $_SESSION['id'];


// For testing, you can set a default user ID if you don't have authentication yet
//$userId = 1; // Replace with actual user ID if you have authentication

try {
    // Start transaction
    $pdo->beginTransaction();

    // Check if seats are available
    $seatCheckStmt = $pdo->prepare("SELECT seat_number, status FROM seats WHERE event_id = ? AND seat_number IN (" .
        implode(',', array_fill(0, count($selectedSeats), '?')) . ")");

    $params = array_merge([$eventId], $selectedSeats);
    $seatCheckStmt->execute($params);
    $seats = $seatCheckStmt->fetchAll(PDO::FETCH_ASSOC);

    // Verify all seats are available
    foreach ($seats as $seat) {
        if ($seat['status'] !== 'available') {
            $pdo->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Seat ' . $seat['seat_number'] . ' is no longer available'
            ]);
            exit;
        }
    }

    // Update seats to reserved
    $updateStmt = $pdo->prepare("UPDATE seats SET status = 'reserved' WHERE event_id = ? AND seat_number = ?");

    foreach ($selectedSeats as $seatNumber) {
        $updateStmt->execute([$eventId, $seatNumber]);
    }

    // Update available_seats count in events table
    $seatCount = count($selectedSeats);
    $updateEventStmt = $pdo->prepare("UPDATE events SET available_seats = available_seats - ? WHERE id = ?");
    $updateEventStmt->execute([$seatCount, $eventId]);

    // Create a reservation record
    $reference = 'RES-' . strtoupper(bin2hex(random_bytes(4)));

    // Get event price
    $eventStmt = $pdo->prepare("SELECT price FROM events WHERE id = ?");
    $eventStmt->execute([$eventId]);
    $event = $eventStmt->fetch(PDO::FETCH_ASSOC);
    $totalPrice = $event['price'] * $seatCount;

    // Insert reservation
    $reserveStmt = $pdo->prepare("INSERT INTO reservations 
        (user_id, event_id, reservation_date, total_price, payment_status, reference_number) 
        VALUES (?, ?, NOW(), ?, 'pending', ?)");

    $reserveStmt->execute([
        $userId,
        $eventId,
        $totalPrice,
        $reference
    ]);

    $reservationId = $pdo->lastInsertId();

    // Insert reservation details
    foreach ($selectedSeats as $seatNumber) {
        $seatStmt = $pdo->prepare("SELECT id FROM seats WHERE event_id = ? AND seat_number = ?");
        $seatStmt->execute([$eventId, $seatNumber]);
        $seat = $seatStmt->fetch(PDO::FETCH_ASSOC);
        $seatId = $seat['id'];

        $reservationDetailsStmt = $pdo->prepare("INSERT INTO reservation_details (reservation_id, seat_id) VALUES (?, ?)");
        $reservationDetailsStmt->execute([$reservationId, $seatId]);
    }

    // Commit the transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Seats reserved successfully',
        'reservation_id' => $reservationId,
        'reference' => $reference
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>