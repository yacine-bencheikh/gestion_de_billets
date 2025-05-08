<?php
// create_event.php in the organisation folder
session_start();

// Check if user is logged in and is an organizer
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'organizer') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Include database connection
require_once '../includes/db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $date = $_POST['date'];
    $duration = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_NUMBER_INT);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $totalSeats = filter_input(INPUT_POST, 'totalSeats', FILTER_SANITIZE_NUMBER_INT);
    $availableSeats = filter_input(INPUT_POST, 'availableSeats', FILTER_SANITIZE_NUMBER_INT);
    $description = htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8');
    $organizerId = $_SESSION['id'];
    $imageUrl = null;  // Default to null

    // Handle image upload if an image was provided
    if (isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/events/';  // Create this directory if it doesn't exist

        // Create the directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate a unique filename
        $fileExtension = pathinfo($_FILES['eventImage']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('event_') . '.' . $fileExtension;
        $targetFilePath = $uploadDir . $fileName;

        // Validate the file
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileExtension), $validExtensions)) {
            // Check file size (max 2MB)
            if ($_FILES['eventImage']['size'] <= 2000000) {
                // Move the file to the uploads directory
                if (move_uploaded_file($_FILES['eventImage']['tmp_name'], $targetFilePath)) {
                    $imageUrl = 'uploads/events/' . $fileName;  // Store relative path
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                    exit;
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Image size exceeds 2MB limit']);
                exit;
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid image format. Only JPG, JPEG, PNG and GIF are allowed']);
            exit;
        }
    }

    // Get seats configuration from JSON data
    $seatsConfig = json_decode($_POST['seatsConfig'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid seat configuration format']);
        exit;
    }
    error_log("Seats config: " . json_encode($seatsConfig));

    // Insert event into database
    try {
        error_log("Event data: " . json_encode($_POST));

        $pdo->beginTransaction();

        // Insert event record - added image_url column
        $stmt = $pdo->prepare("INSERT INTO events (organizer_id, title, description, event_date, duration, price, total_seats, available_seats, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$organizerId, $title, $description, $date, $duration, $price, $totalSeats, $availableSeats, $imageUrl]);

        // Get the new event ID
        $eventId = $pdo->lastInsertId();
        error_log("New event ID: " . $eventId);

        // Insert seats
        $seatStmt = $pdo->prepare("INSERT INTO seats (event_id, seat_number, status) VALUES (?, ?, ?)");

        foreach ($seatsConfig as $seat) {
            try {
                error_log("Inserting seat: " . json_encode($seat));
                $seatStmt->execute([$eventId, $seat['id'], $seat['status']]);
            } catch (Exception $seatEx) {
                error_log("Seat insertion error for seat " . json_encode($seat) . ": " . $seatEx->getMessage());
                throw $seatEx; // Re-throw to trigger the outer catch block
            }
        }

        $pdo->commit();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Event created successfully!']);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Event creation error: " . $e->getMessage());

        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
} else {
    // Redirect if not a POST request
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
?>