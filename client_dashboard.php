<?php
session_start();
// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
    header('Location: auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - TicketThéâtre</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">TicketThéâtre</div>
    <div class="nav-links">
        <a href="index.php">Accueil</a>
        <a href="events.php">Événements</a>
        <a href="my_tickets.php">Mes Tickets</a>
        <a href="profile.php">Mon Profil</a>
        <a href="auth/logout.php">Déconnexion</a>
    </div>
</nav>

<div class="container">
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_name']); ?> !</h1>
    <p>Voici votre tableau de bord client. Ici, vous pouvez consulter vos billets, les événements à venir, et plus encore.</p>

    <div class="dashboard-cards">
        <div class="card">
            <h2>Mes Événements à Venir</h2>
            <p>Vous n'avez pas d'événements à venir.</p>
            <a href="events.php" class="btn">Parcourir les Événements</a>
        </div>

        <div class="card">
            <h2>Mes Tickets</h2>
            <p>Vous n'avez pas encore de tickets.</p>
            <a href="events.php" class="btn">Acheter des Tickets</a>
        </div>

        <div class="card stats-card">
            <div class="number">0</div>
            <div class="label">Tickets Achetés</div>
        </div>

        <div class="card stats-card">
            <div class="number">0</div>
            <div class="label">Événements Visités</div>
        </div>
    </div>

    <h2 style="margin-top: 2rem;">Événements Recommandés</h2>
    <div class="event-cards">
        <div class="event-card">
            <div class="event-image" style="background-color: #3498db;"></div>
            <div class="event-details">
                <h3>Concert de Jazz</h3>
                <div class="event-meta">15 Avril 2023 • 20:00</div>
                <div class="event-price">45,00 €</div>
                <a href="#" class="btn">Voir les Détails</a>
            </div>
        </div>

        <div class="event-card">
            <div class="event-image" style="background-color: #e74c3c;"></div>
            <div class="event-details">
                <h3>Pièce de Théâtre</h3>
                <div class="event-meta">22 Avril 2023 • 19:30</div>
                <div class="event-price">35,00 €</div>
                <a href="#" class="btn">Voir les Détails</a>
            </div>
        </div>

        <div class="event-card">
            <div class="event-image" style="background-color: #2ecc71;"></div>
            <div class="event-details">
                <h3>Ballet Classique</h3>
                <div class="event-meta">30 Avril 2023 • 18:00</div>
                <div class="event-price">55,00 €</div>
                <a href="#" class="btn">Voir les Détails</a>
            </div>
        </div>
    </div>
</div>

<script>
    // You can add JavaScript functionality here if needed
</script>
</body>
</html>