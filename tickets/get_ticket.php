<?php
// Start session
session_start();

// Include database connection
require_once '../includes/db_connect.php';

// Check if user is logged in and reservation ID is set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['reservation_id'])) {
    header('Location: ../auth/login.html');
    exit;
}

$reservationId = $_SESSION['reservation_id'];

try {
    // Get reservation details
    $stmt = $pdo->prepare("
        SELECT r.*, e.title, e.event_date, e.duration, p.payment_method, p.card_last_four, p.payment_date
        FROM reservations r
        JOIN events e ON r.event_id = e.id
        JOIN payments p ON p.reservation_id = r.id
        WHERE r.id = ? AND r.user_id = ?
    ");
    $stmt->execute([$reservationId, $_SESSION['user_id']]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        header('Location: ../index.php');
        exit;
    }

    // Get reserved seats
    $seatsStmt = $pdo->prepare("
        SELECT s.row, s.seat_number
        FROM reservation_details rd
        JOIN seats s ON rd.seat_id = s.id
        WHERE rd.reservation_id = ?
    ");
    $seatsStmt->execute([$reservationId]);
    $seats = $seatsStmt->fetchAll();

    // Format seats for display
    $formattedSeats = [];
    foreach ($seats as $seat) {
        $formattedSeats[] = $seat['row'] . $seat['seat_number'];
    }

    // Set ticket data for the view
    $ticketData = [
        'event' => $reservation['title'],
        'date' => $reservation['event_date'],
        'duration' => $reservation['duration'] . ' minutes',
        'seats' => $formattedSeats,
        'total' => number_format($reservation['total_price'], 2, ',', ' ') . ' €',
        'paymentMethod' => $reservation['payment_method'] . ' (•••• ' . $reservation['card_last_four'] . ')',
        'paymentDate' => date('d/m/Y à H:i', strtotime($reservation['payment_date'])),
        'reference' => $reservation['reference_number']
    ];

    // Prepare data for the view
    $_SESSION['ticket_data'] = $ticketData;
} catch (PDOException $e) {
    $_SESSION['error'] = "Failed to retrieve ticket: " . $e->getMessage();
    header('Location: ../index.php');
    exit;
}
?>