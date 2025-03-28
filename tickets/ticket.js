// Récupérer les données de la réservation (simulées ici)
const reservationData = {
    event: "Concert Symphonique",
    date: "15 Mars 2024 - 20:00",
    duration: "2 heures",
    location: "Théâtre Municipal, Paris",
    seats: ["A12", "A13", "A14"],
    total: "135,00 €",
    paymentMethod: "Mastercard (•••• 4242)",
    paymentDate: "25/04/2024 à 14:30",
    reference: "RES-2024-0425"
};

// Dans une implémentation réelle, vous récupéreriez ces données :
// 1. Depuis les paramètres URL
// 2. Depuis le localStorage
// 3. Via une requête API au backend

// Fonction pour générer un vrai QR code (nécessiterait une librairie comme qrcode.js)
function generateQRCode() {
    // Implémentation réelle utiliserait une librairie QR code
    // avec un identifiant unique de réservation
}

// Au chargement de la page
window.onload = function() {
    generateQRCode();
};