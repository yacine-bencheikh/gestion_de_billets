<?php
// Include database connection
require_once 'includes/db_connect.php';

// Get events from database
try {
    $stmt = $pdo->query("SELECT e.*, u.name as organizer_name 
                         FROM events e
                         JOIN users u ON e.organizer_id = u.id
                         WHERE e.event_date > NOW()
                         ORDER BY e.event_date ASC");
    $events = $stmt->fetchAll();

    // Return events as JSON
    header('Content-Type: application/json');
    echo json_encode($events);
} catch (PDOException $e) {
    // Return error as JSON
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to get events: ' . $e->getMessage()]);
}
?>