<?php
session_start();
// Check if user is logged in and is an organizer
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'organizer') {
    header('Location: auth/login.html');

    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - TicketThéâtre</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<nav class="navbar">
    <div class="logo">TicketThéâtre</div>
    <div class="nav-links">
        <a href="index.php">Accueil</a>
        <a href="organisation/org.html">Créer un Événement</a>
        <a href="organisation/manage_events.php">Gérer les Événements</a>
        <a href="sales_reports.php">Rapports de Vente</a>
        <a href="profile.php">Mon Profil</a>
        <a href="auth/logout.php">Déconnexion</a>
    </div>
</nav>²

<div class="container">
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['name']); ?> !</h1>
    <p>Voici votre tableau de bord organisateur. Ici, vous pouvez créer et gérer des événements, consulter les ventes de billets, et plus encore.</p>

    <div class="dashboard-cards">
        <div class="card stats-card">
            <div class="number">0</div>
            <div class="label">Événements Créés</div>
        </div>

        <div class="card stats-card">
            <div class="number">0</div>
            <div class="label">Billets Vendus</div>
        </div>

        <div class="card stats-card">
            <div class="number">0 €</div>
            <div class="label">Revenus Totaux</div>
        </div>

        <div class="card stats-card">
            <div class="number">0</div>
            <div class="label">Clients</div>
        </div>
    </div>

    <div class="event-manager">
        <h2>Vos Événements</h2>
        <p>Vous n'avez pas encore créé d'événements.</p>
        <a href="create_event.php" class="btn">Créer un Nouvel Événement</a>
    </div>

    <div class="card">
        <h2>Actions Rapides</h2>
        <div class="event-action-buttons">
            <a href="create_event.php" class="btn">Créer un Événement</a>
            <a href="manage_events.php" class="btn">Gérer les Événements</a>
            <a href="sales_reports.php" class="btn">Générer un Rapport</a>
            <a href="settings.php" class="btn">Paramètres</a>
        </div>
    </div>

    <h2 style="margin-top: 2rem;">Ventes Récentes</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Événement</th>
            <th>Client</th>
            <th>Date</th>
            <th>Montant</th>
            <th>Statut</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="6" style="text-align: center;">Aucune vente récente</td>
        </tr>
        </tbody>
    </table>
</div>

<script>
    // You can add JavaScript functionality here if needed
</script>
</body>
</html>