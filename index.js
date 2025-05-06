
window.onload = function() {
    // Fetch events from the server
    fetch('get_events.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            renderEvents(data);
        })
        .catch(error => console.error('Error fetching events:', error));
};

function renderEvents(events) {
    const container = document.getElementById('eventsContainer');
    container.innerHTML = ''; // Clear container

    events.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'event-card';

        let availabilityClass = '';
        if (event.available_seats <= 5) {
            availabilityClass = 'low-availability';
        } else if (event.available_seats === 0) {
            availabilityClass = 'sold-out';
        }

        eventCard.innerHTML = `
            <img src="${event.image_url || 'https://via.placeholder.com/500x300'}" alt="${event.title}" class="event-image">
            <div class="event-content">
                <h2 class="event-title">${event.title}</h2>
                <p class="event-date">${formatDate(event.event_date)}</p>
                <div class="event-details">
                    <p>Durée: ${event.duration} minutes</p>
                    <p>Prix: ${event.price}€</p>
                    <p>Places disponibles: ${event.available_seats}/${event.total_seats}</p>
                    <p>Catégorie: ${event.category || 'Non spécifié'}</p>
                </div>
                
                <button class="buy-button" onclick="window.location.href='./Reservation/reservation.html?event=${event.id}'">
                    ${event.available_seats > 0 ? 'Acheter un billet' : 'Voir détails'}
                </button>
            </div>
        `;

        container.appendChild(eventCard);
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}