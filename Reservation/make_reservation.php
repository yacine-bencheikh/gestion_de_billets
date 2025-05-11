<?php
// Include database connection
require_once '../includes/db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../login.php');
    exit;
}

// Get event ID from URL parameter
$eventId = filter_input(INPUT_GET, 'event', FILTER_SANITIZE_NUMBER_INT);

if (!$eventId) {
    $_SESSION['error'] = 'Invalid event ID';
    header('Location: ../events/index.php');
    exit;
}

// Get event details
try {
    $eventStmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $eventStmt->execute([$eventId]);
    $event = $eventStmt->fetch();

    if (!$event) {
        $_SESSION['error'] = 'Event not found';
        header('Location: ../events/index.php');
        exit;
    }

    // Check if seats are available
    if ($event['available_seats'] <= 0) {
        $_SESSION['error'] = 'Sorry, no seats available for this event.';
        header('Location: ../events/details.php?id=' . $eventId);
        exit;
    }

    // Redirect to reserve_seats.php to continue the booking process
    header('Location: reserve_seats.php?event=' . $eventId);
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: ../events/index.php');
    exit;
}
?>