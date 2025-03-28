function processPayment(e) {
    e.preventDefault();

    // Validation basique
    const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
    const cvv = document.getElementById('cvv').value;

    if (!validateCardNumber(cardNumber)) {
        alert('Numéro de carte invalide');
        return false;
    }

    if (!validateCVV(cvv)) {
        alert('CVV invalide');
        return false;
    }

    // Simulation de paiement réussi
    alert('Paiement réussi ! see your ticket...');
    window.location.href = '../tickets/tickets.html';
    return false;
}

function validateCardNumber(number) {
    // Algorithme de Luhn simplifié
    return /^\d{13,16}$/.test(number);
}

function validateCVV(cvv) {
    return /^\d{3,4}$/.test(cvv);
}

// Formatage automatique du numéro de carte
document.getElementById('cardNumber').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\s/g, '');
    if (value.length > 16) value = value.slice(0, 16);
    e.target.value = value.match(/.{1,4}/g)?.join(' ') || '';
});