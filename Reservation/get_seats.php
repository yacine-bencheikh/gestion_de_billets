<?php
// Include database connection
require_once '../includes/db_connect.php';

// Get event ID from URL parameter
$eventId = filter_input(INPUT_GET, 'event', FILTER_SANITIZE_NUMBER_INT);

if (!$eventId) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid event ID']);
    exit;
}

try {
    // Get event details
    $eventStmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $eventStmt->execute([$eventId]);
    $event = $eventStmt->fetch();

    if (!$event) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Event not found']);
        exit;
    }

    // Get seats for the event
    $seatsStmt = $pdo->prepare("SELECT * FROM seats WHERE event_id = ?");
    $seatsStmt->execute([$eventId]);
    $seats = $seatsStmt->fetchAll();

    // Format seats for the front-end
    $formattedSeats = [];
    foreach ($seats as $seat) {
        $formattedSeats[] = [
            'id' => $seat['row'] . $seat['seat_number'],
            'status' => $seat['status']
        ];
    }

    // Return event data and seats as JSON
    $responseData = [
        'event' => $event,
        'seats' => $formattedSeats
    ];

    header('Content-Type: application/json');
    echo json_encode($responseData);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to get seats: ' . $e->getMessage()]);
}
?>