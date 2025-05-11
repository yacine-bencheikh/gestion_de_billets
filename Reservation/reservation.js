
let selectedSeats = [];

window.onload = function() {
    // Get event ID from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const eventId = urlParams.get('event');

    if (!eventId) {
        alert("Event ID not specified!");
        window.location.href = '../index.php';
        return;
    }

    // Fetch event and seat data from the server
    fetch(`get_seats.php?event=${eventId}`)
        .then(response => {
            console.log("Response status:", response.status);
            return response.json();
        })
        .then(data => {
            console.log("Data received:", data);

            if (data.error) {
                alert(data.error);
                window.location.href = '../index.php';
                return;
            }

            // Set event data
            eventData = {
                ...data.event,
                seats: data.seats
            };

            console.log("Event data processed:", eventData);
            console.log("Seats available:", eventData.seats);

            // Update page title with event name
            document.querySelector('h1').textContent = `${eventData.title} - Sélection des Places`;

            // Generate seat map
            generateSeatMap();
        })
        .catch(error => {
            console.error('Error fetching seats:', error);
            alert("Failed to load seat data. Please try again later.");
        });

// Also add logging in generateSeatMap function
    function generateSeatMap() {
        const seatMap = document.getElementById('seatMap');
        seatMap.innerHTML = '';

        console.log("Generating seat map with seats:", eventData.seats);

        if (!eventData.seats || eventData.seats.length === 0) {
            console.error("No seats available in the data");
            seatMap.innerHTML = '<p>Aucune place disponible pour cet événement.</p>';
            return;
        }

        eventData.seats.forEach(seat => {
            console.log("Processing seat:", seat);
            const seatElement = document.createElement('button');
            seatElement.className = `seat ${seat.status}`;
            seatElement.textContent = seat.id;

            if (seat.status === 'available') {
                seatElement.onclick = () => toggleSeatSelection(seat.id);
            }

            seatMap.appendChild(seatElement);
        });
    }
    document.getElementById('reserveButton').addEventListener('click', function() {
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
    });




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

   
    document.querySelectorAll('.seat').forEach(seat => {
        seat.classList.remove('selected');
        if (selectedSeats.includes(seat.textContent)) {
            seat.classList.add('selected');
        }
    });
}







    function hideModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }
};
