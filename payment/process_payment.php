<?php
// Start session
session_start();

// Include database connection
require_once '../includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $eventId = filter_input(INPUT_POST, 'eventId', FILTER_SANITIZE_NUMBER_INT);
    $selectedSeats = json_decode($_POST['selectedSeats'], true);
    $totalPrice = filter_input(INPUT_POST, 'totalPrice', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $cardNumber = filter_input(INPUT_POST, 'cardNumber', FILTER_SANITIZE_STRING);
    $cardName = filter_input(INPUT_POST, 'cardName', FILTER_SANITIZE_STRING);

    // Get last 4 digits of card for storage
    $cardLastFour = substr(str_replace(' ', '', $cardNumber), -4);

    // Generate a reference number
    $referenceNumber = 'RES-' . date('Y') . '-' . sprintf('%04d', mt_rand(1, 9999));

    try {
        $pdo->beginTransaction();

        // Create reservation
        $stmt = $pdo->prepare("INSERT INTO reservations 
                              (user_id, event_id, reservation_date, total_price, payment_status, reference_number) 
                              VALUES (?, ?, NOW(), ?, 'completed', ?)");
        $stmt->execute([$_SESSION['user_id'], $eventId, $totalPrice, $referenceNumber]);

        // Get the new reservation ID
        $reservationId = $pdo->lastInsertId();

        // Process each selected seat
        $updateSeatStmt = $pdo->prepare("UPDATE seats SET status = 'reserved' WHERE event_id = ? AND row = ? AND seat_number = ?");
        $addDetailStmt = $pdo->prepare("INSERT INTO reservation_details (reservation_id, seat_id) VALUES (?, ?)");

        foreach ($selectedSeats as $seatId) {
            // Extract row and seat number (e.g., "A12" -> row "A", seat "12")
            $row = preg_replace('/[0-9]+/', '', $seatId);
            $seatNumber = preg_replace('/[^0-9]+/', '', $seatId);

            // Update seat status
            $updateSeatStmt->execute([$eventId, $row, $seatNumber]);

            // Get the seat ID
            $seatIdStmt = $pdo->prepare("SELECT id FROM seats WHERE event_id = ? AND row = ? AND seat_number = ?");
            $seatIdStmt->execute([$eventId, $row, $seatNumber]);
            $seatIdResult = $seatIdStmt->fetch();

            // Add reservation detail
            $addDetailStmt->execute([$reservationId, $seatIdResult['id']]);
        }

        // Update available seats count in events table
        $updateEventStmt = $pdo->prepare("UPDATE events 
                                          SET available_seats = available_seats - ? 
                                          WHERE id = ?");
        $updateEventStmt->execute([count($selectedSeats), $eventId]);

        // Record payment
        $paymentStmt = $pdo->prepare("INSERT INTO payments 
                                     (reservation_id, amount, payment_method, card_last_four, payment_date, status) 
                                     VALUES (?, ?, ?, ?, NOW(), 'completed')");
        $paymentStmt->execute([$reservationId, $totalPrice, 'Credit Card', $cardLastFour]);

        $pdo->commit();

        // Store reservation ID in session for ticket page
        $_SESSION['reservation_id'] = $reservationId;

        // Return success response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'reservation_id' => $reservationId,
            'reference' => $referenceNumber
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();

        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Payment processing failed: ' . $e->getMessage()]);
    }
} else {
    // Return error for non-POST requests
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request method']);
}
?>