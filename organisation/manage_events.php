<?php
session_start();
// Check if user is logged in and is an organizer
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'organizer') {
    header('Location: auth/login.html');
    exit;
}

// Include database connection
require_once '../includes/db_connect.php';

// Process form submissions for updating event status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $eventId = filter_input(INPUT_POST, 'event_id', FILTER_SANITIZE_NUMBER_INT);
    
    // Update event status (cancel, postpone, etc.)
    if ($_POST['action'] === 'update_status' && isset($_POST['new_status'])) {
        $newStatus = htmlspecialchars($_POST['new_status'], ENT_QUOTES, 'UTF-8');
        $stmt = $pdo->prepare("UPDATE events SET status = ? WHERE id = ? AND organizer_id = ?");
        $stmt->execute([$newStatus, $eventId, $_SESSION['id']]);
        
        if ($stmt->rowCount() > 0) {
            $statusMessage = "Le statut de l'événement a été mis à jour avec succès.";
        } else {
            $statusError = "Impossible de mettre à jour le statut de l'événement.";
        }
    }
    
    // Update image URL
    else if ($_POST['action'] === 'update_image' && isset($_POST['image_url'])) {
        $imageUrl = htmlspecialchars($_POST['image_url'], ENT_QUOTES, 'UTF-8');
        $stmt = $pdo->prepare("UPDATE events SET image_url = ? WHERE id = ? AND organizer_id = ?");
        $stmt->execute([$imageUrl, $eventId, $_SESSION['id']]);
        
        if ($stmt->rowCount() > 0) {
            $imageMessage = "L'image de l'événement a été mise à jour avec succès.";
        } else {
            $imageError = "Impossible de mettre à jour l'image de l'événement.";
        }
    }
}

// Get all events for the current organizer
$stmt = $pdo->prepare("SELECT * FROM events WHERE organizer_id = ? ORDER BY event_date DESC");
$stmt->execute([$_SESSION['id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Événements - TicketThéâtre</title>
    <link rel="stylesheet" href="org.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">TicketThéâtre</div>
    <div class="nav-links">
        <a href="index.php">Accueil</a>
        <a href="organisation/org.html">Créer un Événement</a>
        <a href="manage_events.php" class="active">Gérer les Événements</a>
        <a href="sales_reports.php">Rapports de Vente</a>
        <a href="profile.php">Mon Profil</a>
        <a href="auth/logout.php">Déconnexion</a>
    </div>
</nav>

<div class="page-container">
    <h1>Gérer Mes Événements</h1>
    
    <?php if (isset($statusMessage)): ?>
        <div class="alert alert-success"><?php echo $statusMessage; ?></div>
    <?php endif; ?>
    
    <?php if (isset($statusError)): ?>
        <div class="alert alert-danger"><?php echo $statusError; ?></div>
    <?php endif; ?>
    
    <?php if (isset($imageMessage)): ?>
        <div class="alert alert-success"><?php echo $imageMessage; ?></div>
    <?php endif; ?>
    
    <?php if (isset($imageError)): ?>
        <div class="alert alert-danger"><?php echo $imageError; ?></div>
    <?php endif; ?>
    
    <?php if (empty($events)): ?>
        <p>Vous n'avez pas encore créé d'événements.</p>
        <a href="organisation/org.html" class="btn">Créer un Nouvel Événement</a>
    <?php else: ?>
        <div class="events-container">
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <?php if (!empty($event['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-image">
                    <?php else: ?>
                        <div class="event-image" style="background-color: #eee; display: flex; align-items: center; justify-content: center;">
                            <span>Aucune image</span>
                        </div>
                    <?php endif; ?>
                    
                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                    
                    <div class="event-details">
                        <div class="detail-row">
                            <span>Date:</span>
                            <span><?php echo date('d/m/Y H:i', strtotime($event['event_date'])); ?></span>
                        </div>
                        <div class="detail-row">
                            <span>Prix:</span>
                            <span><?php echo number_format($event['price'], 2); ?> €</span>
                        </div>
                        <div class="detail-row">
                            <span>Places:</span>
                            <span><?php echo $event['available_seats']; ?>/<?php echo $event['total_seats']; ?></span>
                        </div>
                        <div class="detail-row">
                            <span>Statut:</span>
                            <span class="status-<?php echo strtolower($event['status'] ?? 'active'); ?>">
                                <?php echo htmlspecialchars($event['status'] ?? 'Actif'); ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Update Status Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <input type="hidden" name="action" value="update_status">
                        
                        <div class="form-group">
                            <label for="new_status_<?php echo $event['id']; ?>">Mettre à jour le statut:</label>
                            <select id="new_status_<?php echo $event['id']; ?>" name="new_status" class="form-control">
                                <option value="active" <?php echo ($event['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Actif</option>
                                <option value="cancelled" <?php echo ($event['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Annulé</option>
                                <option value="postponed" <?php echo ($event['status'] ?? '') === 'postponed' ? 'selected' : ''; ?>>Reporté</option>
                                <option value="sold_out" <?php echo ($event['status'] ?? '') === 'sold_out' ? 'selected' : ''; ?>>Complet</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn">Mettre à jour le statut</button>
                    </form>
                    
                    <!-- Update Image Form -->
                    <form method="POST" action="" style="margin-top: 15px;">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <input type="hidden" name="action" value="update_image">
                        
                        <div class="form-group">
                            <label for="image_url_<?php echo $event['id']; ?>">URL de l'image:</label>
                            <input type="text" id="image_url_<?php echo $event['id']; ?>" name="image_url" 
                                   value="<?php echo htmlspecialchars($event['image_url'] ?? ''); ?>" 
                                   class="form-control" placeholder="https://...">
                        </div>
                        
                        <button type="submit" class="btn">Mettre à jour l'image</button>
                    </form>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    // You can add JavaScript functionality here if needed
</script>
</body>
</html>