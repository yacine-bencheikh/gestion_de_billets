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

    // Check if you have a seats table
    $hasSeatsTable = false;
    try {
        $checkTable = $pdo->query("SELECT 1 FROM seats LIMIT 1");
        $hasSeatsTable = true;
    } catch (PDOException $e) {
        // Table doesn't exist
        $hasSeatsTable = false;
    }

    if ($hasSeatsTable) {
        // If you have a seats table, get seats for the event
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

        $responseData = [
            'event' => $event,
            'seats' => $formattedSeats
        ];
    } else {
        // If you don't have a seats table, just return available seats info
        $responseData = [
            'event' => $event,
            'available_seats' => $event['available_seats'],
            'total_seats' => $event['total_seats']
        ];
    }

    // Return event data and seats as JSON
    header('Content-Type: application/json');
    echo json_encode($responseData);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to get seats: ' . $e->getMessage()]);
}
?>