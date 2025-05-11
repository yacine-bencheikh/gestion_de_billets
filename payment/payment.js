// payment.js - fixed version

// Declare reservationId variable
let reservationId = null;

window.onload = function() {
    // Get reservation ID from the hidden input field
    const reservationIdInput = document.getElementById('reservationId');
    if (reservationIdInput) {
        reservationId = reservationIdInput.value;
    }

    // If still null, try to get from URL
    if (!reservationId) {
        const urlParams = new URLSearchParams(window.location.search);
        reservationId = urlParams.get('reservation');
    }

    console.log('Retrieved Reservation ID:', reservationId);

    if (!reservationId) {
        alert('Information de réservation manquante');
        window.location.href = '../index.php';
        return;
    }

    // Fetch reservation details to populate order summary
    fetchReservationDetails();
};

function fetchReservationDetails() {
    fetch(`get_reservation_details.php?reservation=${reservationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateOrderSummary(data.reservation);
            } else {
                console.error('Failed to fetch reservation details:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching reservation details:', error);
        });
}

function updateOrderSummary(reservation) {
    const summaryDiv = document.querySelector('.order-summary');
    if (summaryDiv && reservation) {
        summaryDiv.innerHTML = `
            <h3>Votre commande</h3>
            <div class="summary-item">
                <p><strong>Événement:</strong></p>
                <p>${reservation.event_title}</p>
                <p>Date: ${reservation.event_date}</p>
            </div>
            <div class="summary-item">
                <p><strong>Places sélectionnées:</strong></p>
                <p>${reservation.selected_seats.join(', ')}</p>
            </div>
            <div class="summary-item">
                <p><strong>Total:</strong></p>
                <p style="font-size: 1.5rem; color: #27ae60;">${reservation.total_price}€</p>
            </div>
        `;
    }
}

function processPayment(e) {
    e.preventDefault();
    console.log('Processing payment for reservation:', reservationId);

    if (!reservationId) {
        alert('Information de réservation manquante');
        return false;
    }

    const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
    const cardName = document.getElementById('cardName').value;
    const cvv = document.getElementById('cvv').value;
    const expMonth = document.getElementById('expMonth').value;
    const expYear = document.getElementById('expYear').value;

    // Basic validation
    if (!validateCardNumber(cardNumber)) {
        alert('Numéro de carte invalide');
        return false;
    }

    if (!validateCVV(cvv)) {
        alert('CVV invalide');
        return false;
    }

    if (!expMonth || !expYear) {
        alert('Veuillez sélectionner une date d\'expiration valide');
        return false;
    }

    // Create payment data
    const paymentData = {
        reservation_id: reservationId,
        payment_method: 'credit_card',
        card_last_four: cardNumber.slice(-4),
        card_holder: cardName
    };

    console.log('Sending payment data:', paymentData);

    // Submit payment to server
    fetch('process_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify(paymentData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Payment response:', data);
        if (data.success) {
            // Redirect to confirmation page
            window.location.href = `confirmation.php?reservation=${reservationId}`;
        } else {
            alert(`Erreur de paiement: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors du traitement du paiement. Veuillez réessayer.');
    });

    return false;
}

function validateCardNumber(number) {
    return /^\d{13,16}$/.test(number);
}

function validateCVV(cvv) {
    return /^\d{3,4}$/.test(cvv);
}

// Format card number with spaces
document.getElementById('cardNumber').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    if (value.length > 16) value = value.slice(0, 16);
    e.target.value = value.match(/.{1,4}/g)?.join(' ') || '';
});