// Données simulées
const statuses = ["available", "reserved", "damaged"];
const eventData = {
    price: 45,
    availableSeats: 23,
    seats: generateSeats()
};

function generateSeats() {
    const seats = [];
    const rows = ['A', 'B', 'C', 'D', 'E','F']; // Add more rows as needed
    const seatsPerRow = 15;

    rows.forEach(row => {
        for (let i = 1; i <= seatsPerRow; i++) {
            const seatId = `${row}${i}`;
            const status = getRandomStatus();
            seats.push({ id: seatId, status: status });
        }
    });

    return seats;
}

function getRandomStatus() {
    const random = Math.random();
    if (random < 0.7) {
        return "available";
    } else if (random < 0.9) {
        return "reserved";
    } else {
        return "damaged";
    }
}

let selectedSeats = [];

function generateSeatMap() {
    const seatMap = document.getElementById('seatMap');

    eventData.seats.forEach(seat => {
        const seatElement = document.createElement('button');
        seatElement.className = `seat ${seat.status}`;
        seatElement.textContent = seat.id;

        if (seat.status === 'available') {
            seatElement.onclick = () => toggleSeatSelection(seat.id);
        }

        seatMap.appendChild(seatElement);
    });
}

function toggleSeatSelection(seatId) {
    const index = selectedSeats.indexOf(seatId);

    if (index === -1) {
        if (selectedSeats.length >= eventData.availableSeats) {
            alert("Vous ne pouvez pas sélectionner plus de places disponibles !");
            return;
        }
        selectedSeats.push(seatId);
    } else {
        selectedSeats.splice(index, 1);
    }

    updateSelectionDisplay();
}

function updateSelectionDisplay() {
    document.getElementById('selectedCount').textContent = selectedSeats.length;
    document.getElementById('totalPrice').textContent = selectedSeats.length * eventData.price;

    // Mise à jour visuelle
    document.querySelectorAll('.seat').forEach(seat => {
        seat.classList.remove('selected');
        if (selectedSeats.includes(seat.textContent)) {
            seat.classList.add('selected');
        }
    });
}

function showConfirmation() {
    if (selectedSeats.length === 0) {
        alert("Veuillez sélectionner au moins une place !");
        return;
    }

    const modal = document.getElementById('confirmModal');
    modal.style.display = 'block';
    document.getElementById('modalDetails').innerHTML = `
        Places: ${selectedSeats.join(', ')}<br>
        Total: ${selectedSeats.length * eventData.price}€
    `;
}

function confirmReservation() {
    // Ici ajouter la logique de réservation avec PHP
    alert(`Réservation confirmée pour: ${selectedSeats.join(', ')}`);
    hideModal();
    selectedSeats = [];
    updateSelectionDisplay();
}

function hideModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

// Initialisation
window.onload = generateSeatMap;