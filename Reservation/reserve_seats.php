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

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);

        // Validate quantity
        if (!$quantity || $quantity < 1) {
            $_SESSION['error'] = 'Please select a valid number of tickets.';
            header('Location: reserve_seats.php?event=' . $eventId);
            exit;
        }

        // Check if enough seats are available
        if ($event['available_seats'] < $quantity) {
            $_SESSION['error'] = 'Not enough seats available.';
            header('Location: reserve_seats.php?event=' . $eventId);
            exit;
        }

        // Start a transaction
        $pdo->beginTransaction();

        try {
            // Generate reference number
            $reference = 'RES-' . strtoupper(bin2hex(random_bytes(5)));

            // Calculate total price
            $totalPrice = $event['price'] * $quantity;

            // Create reservation
            $reserveStmt = $pdo->prepare(
                "INSERT INTO reservations 
                (user_id, event_id, ticket_quantity, reservation_date, total_price, payment_status, reference_number) 
                VALUES (?, ?, ?, NOW(), ?, 'pending', ?)"
            );

            $reserveStmt->execute([
                $_SESSION['user_id'],
                $eventId,
                $quantity,
                $totalPrice,
                $reference
            ]);

            $reservationId = $pdo->lastInsertId();

            // Update available seats
            $updateStmt = $pdo->prepare(
                "UPDATE events SET available_seats = available_seats - ? WHERE id = ?"
            );
            $updateStmt->execute([$quantity, $eventId]);

            $pdo->commit();

            // Redirect to payment page
            header('Location: ../payment/process.php?reservation=' . $reservationId);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Failed to create reservation: ' . $e->getMessage();
            header('Location: reserve_seats.php?event=' . $eventId);
            exit;
        }
    }

} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: ../events/index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Tickets - <?= htmlspecialchars($event['title']) ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>Reserve Tickets for <?= htmlspecialchars($event['title']) ?></h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="event-details">
        <h2>Event Details</h2>
        <p><strong>Date:</strong> <?= date('F d, Y - h:i A', strtotime($event['event_date'])) ?></p>
        <p><strong>Duration:</strong> <?= htmlspecialchars($event['duration']) ?> minutes</p>
        <p><strong>Price:</strong> $<?= number_format($event['price'], 2) ?> per ticket</p>
        <p><strong>Available Seats:</strong> <?= htmlspecialchars($event['available_seats']) ?></p>
    </div>

    <div class="reservation-form">
        <h2>Reserve Your Tickets</h2>
        <form method="POST" action="reserve_seats.php?event=<?= $eventId ?>">
            <div class="form-group">
                <label for="quantity">How many tickets?</label>
                <input type="number" id="quantity" name="quantity"
                       min="1" max="<?= $event['available_seats'] ?>" value="1" required>
                <small>
                    Total price: <span id="totalPrice">$<?= number_format($event['price'], 2) ?></span>
                </small>
            </div>

            <button type="submit" class="btn">Continue to Payment</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const totalPriceSpan = document.getElementById('totalPrice');
        const pricePerTicket = <?= $event['price'] ?>;

        quantityInput.addEventListener('change', function() {
            const quantity = parseInt(this.value);
            const totalPrice = (quantity * pricePerTicket).toFixed(2);
            totalPriceSpan.textContent = '$' + totalPrice;
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>