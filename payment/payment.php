<?php
session_start();

// Check if user is logged in using the correct session variable
if (!isset($_SESSION['id'])) {
    // Use the correct path to your login page
    header('Location: ../auth/login.html');
    exit;
}

// Get the reservation ID from the URL
$reservationId = isset($_GET['reservation']) ? $_GET['reservation'] : null;

// If no reservation ID is provided, redirect back
if (!$reservationId) {
    header('Location: ../reservation/reservation.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Paiement Sécurisé</title>
    <link rel="stylesheet" href="payment.css">

</head>

<body>
    <nav class="navbar">
        <div class="logo" onclick="window.location.href='../index.php'">TicketThéâtre</div>
        <div class="nav-links">
            <a href="../index.php">Accueil</a>
            <a href="../auth/login.html">Mon Compte</a>
        </div>
    </nav>

    <div class="payment-container">
        <form class="payment-form" id="paymentForm" onsubmit="return processPayment(event)">
            <h2>Paiement Sécurisé</h2>
            <input type="hidden" id="reservationId" name="reservationId" value="<?php echo htmlspecialchars($reservationId); ?>">



            <div class="card-icons">
                <img src="https://img.icons8.com/color/48/000000/visa.png" alt="Visa">
                <img src="https://img.icons8.com/color/48/000000/mastercard.png" alt="Mastercard">
                <img src="https://img.icons8.com/color/48/000000/amex.png" alt="American Express">
            </div>

            <div class="form-group">
                <label>Numéro de carte</label>
                <input type="text" id="cardNumber" placeholder="4242 4242 4242 4242" maxlength="19" required>
            </div>

            <div class="form-group">
                <label>Titulaire de la carte</label>
                <input type="text" id="cardName" placeholder="Jean Dupont" required>
            </div>

            <div class="expiry-cvv">
                <div class="form-group">
                    <label>Date d'expiration</label>
                    <div class="date-inputs">
                        <select id="expMonth" required>
                            <option value="">Mois</option>
                            <option value="01">01 - Janvier</option>
                            <option value="02">02 - Février</option>
                            <option value="03">03 - Mars</option>
                            <option value="04">04 - Avril</option>
                            <option value="05">05 - Mai</option>
                            <option value="06">06 - Juin</option>
                            <option value="07">07 - Juillet</option>
                            <option value="08">08 - Août</option>
                            <option value="09">09 - Septembre</option>
                            <option value="10">10 - Octobre</option>
                            <option value="11">11 - Novembre</option>
                            <option value="12">12 - Décembre</option>
                        </select>
                        <select id="expYear" required>
                            <option value="">Année</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>CVV</label>
                    <input type="text" id="cvv" placeholder="123" maxlength="4" required>
                </div>
            </div>

            <div class="form-group">
                <label>Adresse de facturation</label>
                <input type="text" id="address" placeholder="12 Rue du Théâtre" required>
                <input type="text" id="city" placeholder="Paris" required style="margin-top: 0.5rem;">
                <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                    <input type="text" id="zipCode" placeholder="75001" style="width: 30%;">
                    <select id="country" style="width: 70%;">
                        <option>Tunisia</option>
                        <option>Algeria</option>
                        <option>Palestine</option>
                    </select>
                </div>
            </div>

            <button type="submit">Payer maintenant</button>
            <div class="secured">
                🔒 Paiement sécurisé avec SSL
            </div>
        </form>

        <div class="order-summary">
            <h3>Votre commande</h3>
            <div id="orderDetails">
                <!-- Order details will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script src="payment.js"></script>
</body>

</html>