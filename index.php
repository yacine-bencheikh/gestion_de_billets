<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketThéâtre - Événements</title>
    <link rel="stylesheet" href="style.css">
    <script src="index.js"></script>
</head>
<body>
    
    <nav class="navbar">
        <div class="logo">TicketThéâtre</div>
        <div class="nav-links">
            <?php if (isset($_SESSION['id'])): ?>
                <?php if ($_SESSION['role'] === 'client'): ?>
                    <a href="index.php">Accueil</a>
                    <a href="events.php">Événements</a>
                    <a href="my_tickets.php">Mes Tickets</a>
                    <a href="profile.php">Mon Profil</a>
                    <a href="auth/logout.php">Déconnexion</a>
                <?php elseif ($_SESSION['role'] === 'organizer'): ?>
                    <a href="index.php">Accueil</a>
                    <a href="organisation/org.html">Créer un Événement</a>
                    <a href="organisation/manage_events.php">Gérer les Événements</a>
                    <a href="sales_reports.php">Rapports de Vente</a>
                    <a href="profile.php">Mon Profil</a>
                    <a href="auth/logout.php">Déconnexion</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="./auth/login.html">Connexion</a>
                <a href="./auth/register.html">Inscription</a>
            <?php endif; ?>
        </div>
    </nav>

    
    <div class="container">
        <h1>Événements à venir</h1>
        
        <div class="events-grid" id="eventsContainer">
            
        </div>
    </div>

    
</body>
</html>