let seats = [];
let totalSeats = 0;

document.getElementById('totalSeats').addEventListener('change', function (e) {
    totalSeats = parseInt(e.target.value);
    generateSeatMap(totalSeats);
});

function generateSeatMap(total) {
    const seatMap = document.getElementById('seatMap');
    seatMap.innerHTML = '';
    seats = [];

    for (let i = 0; i < total; i++) {
        const seat = document.createElement('div');
        seat.className = 'seat available';
        seat.textContent = i + 1;
        seat.dataset.id = i + 1;
        seat.dataset.status = 'available';

        seat.addEventListener('click', function () {
            toggleSeatStatus(this);
            updateSeatCount();
        });

        seatMap.appendChild(seat);
        seats.push({
            id: i + 1,
            status: 'available'
        });
    }

    document.getElementById('totalCount').textContent = total;
    updateSeatCount();
}

function toggleSeatStatus(seatElement) {
    const currentStatus = seatElement.dataset.status;
    const newStatus = currentStatus === 'available' ? 'damaged' : 'available';

    seatElement.dataset.status = newStatus;
    seatElement.className = `seat ${newStatus}`;

    // Mettre à jour le tableau seats
    const seatId = parseInt(seatElement.dataset.id);
    seats[seatId - 1].status = newStatus;
}

function updateSeatCount() {
    const available = seats.filter(s => s.status === 'available').length;
    document.getElementById('availableCount').textContent = available;
}

function createEvent(e) {
    e.preventDefault();

    const eventData = {
        name: document.getElementById('eventName').value,
        date: document.getElementById('eventDate').value,
        duration: document.getElementById('duration').value,
        price: parseFloat(document.getElementById('price').value),
        totalSeats: totalSeats,
        availableSeats: seats.filter(s => s.status === 'available').length,
        description: document.getElementById('description').value,
        seatsConfig: seats
    };

    if (eventData.availableSeats === 0) {
        alert("Vous devez avoir au moins une place disponible !");
        return false;
    }

    console.log('Événement créé:', eventData);
    alert('Événement créé avec succès !');
    window.location.href = 'organizer_dashboard.html';

    return false;
}