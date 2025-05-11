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


    const seatId = parseInt(seatElement.dataset.id);
    seats[seatId - 1].status = newStatus;
}

function updateSeatCount() {
    const available = seats.filter(s => s.status === 'available').length;
    document.getElementById('availableCount').textContent = available;
}

function submitEventForm(e) {
    e.preventDefault();

    const eventData = {
        name: document.getElementById('eventName').value,
        date: document.getElementById('eventDate').value,
        duration: document.getElementById('duration').value,
        price: parseFloat(document.getElementById('price').value),
        totalSeats: totalSeats,
        availableSeats: seats.filter(s => s.status === 'available').length,
        description: document.getElementById('description').value,
        seatsConfig: JSON.stringify(seats)
    };

    if (eventData.availableSeats === 0) {
        alert("Vous devez avoir au moins une place disponible !");
        return false;
    }

    // Create FormData object for AJAX request
    const formData = new FormData();
    for (const key in eventData) {
        formData.append(key, eventData[key]);
    }

    // Append the image file to the form data if provided
    const imageInput = document.getElementById('eventImage');
    if (imageInput.files.length > 0) {
        formData.append('eventImage', imageInput.files[0]);
    }

    // Send data to server
    fetch('./create_event.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Événement créé avec succès !');
                window.location.href = '../organizer_dashboard.php';
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error details:', error);
            alert('Une erreur est survenue lors de la création de l\'événement: ' + error.message);
        });

    return false;
}