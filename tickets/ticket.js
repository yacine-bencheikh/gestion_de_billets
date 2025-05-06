window.onload = function() {
    // Fetch ticket data
    fetch('get_ticket.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                window.location.href = '../index.php';
                return;
            }

            // Populate ticket with data
            populateTicket(data);
            generateQRCode(data.reference);
        })
        .catch(error => {
            console.error('Error fetching ticket data:', error);
        });
};

function populateTicket(data) {
    document.querySelector('.ticket-header h2').textContent = data.event;
    document.querySelector('.ticket-number').textContent = '#' + data.reference;

    // Populate event details
    const eventInfo = document.querySelectorAll('.ticket-info p');
    eventInfo[0].innerHTML = `<strong>Date :</strong> ${formatDate(data.date)}`;
    eventInfo[1].innerHTML = `<strong>Durée :</strong> ${data.duration}`;

    // Populate seats
    const seatsContainer = document.querySelector('.ticket-info div');
    seatsContainer.innerHTML = '';
    data.seats.forEach(seat => {
        const seatBadge = document.createElement('span');
        seatBadge.className = 'seat-badge';
        seatBadge.textContent = seat;
        seatsContainer.appendChild(seatBadge);
    });

    // Populate payment info
    eventInfo[3].innerHTML = `<strong>Montant total :</strong> ${data.total}`;
    eventInfo[4].innerHTML = `<strong>Méthode :</strong> ${data.paymentMethod}`;
    eventInfo[5].innerHTML = `<strong>Date de paiement :</strong> ${data.paymentDate}`;
}

function generateQRCode(reference) {
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${reference}`;
    document.querySelector('#qrcode img').src = qrCodeUrl
}