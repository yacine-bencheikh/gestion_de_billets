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

// Get user's reservations
try {
    $reservationsStmt = $pdo->prepare(
        "SELECT r.*, e.title, e.event_date, p.status as payment_status
         FROM reservations r
         JOIN events e ON r.event_id = e.id
         LEFT JOIN payments p ON r.id = p.reservation_id
         WHERE r.user_id = ?
         ORDER BY r.reservation_date DESC"
    );
    $reservationsStmt->execute([$_SESSION['id']]);
    $reservations = $reservationsStmt->fetchAll();

} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    $reservations = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>My Reservations</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($reservations)): ?>
        <div class="empty-state">
            <p>You don't have any reservations yet.</p>
            <a href="../events/index.php" class="btn">Browse Events</a>
        </div>
    <?php else: ?>
        <div class="reservations-list">
            <?php foreach ($reservations as $reservation): ?>
                <div class="reservation-card">
                    <div class="reservation-header">
                        <h3><?= htmlspecialchars($reservation['title']) ?></h3>
                        <span class="status status-<?= strtolower($reservation['payment_status'] ?? 'pending') ?>">
                                <?= ucfirst($reservation['payment_status'] ?? 'pending') ?>
                            </span>
                    </div>

                    <div class="reservation-details">
                        <p><strong>Date:</strong> <?= date('F d, Y - h:i A', strtotime($reservation['event_date'])) ?></p>
                        <p><strong>Tickets:</strong> <?= htmlspecialchars($reservation['ticket_quantity']) ?></p>
                        <p><strong>Total:</strong> $<?= number_format($reservation['total_price'], 2) ?></p>
                        <p><strong>Reference:</strong> <?= htmlspecialchars($reservation['reference_number']) ?></p>
                        <p><strong>Reserved on:</strong> <?= date('M d, Y', strtotime($reservation['reservation_date'])) ?></p>
                    </div>

                    <div class="reservation-actions">
                        <?php if (($reservation['payment_status'] ?? 'pending') === 'pending'): ?>
                            <a href="../payment/process.php?reservation=<?= $reservation['id'] ?>" class="btn btn-primary">Complete Payment</a>
                        <?php elseif (($reservation['payment_status'] ?? '') === 'completed'): ?>
                            <a href="../payment/confirmation.php?reservation=<?= $reservation['id'] ?>" class="btn btn-secondary">View Confirmation</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>