const events = [
    {
        title: "Concert Symphonique",
        date: "15 Mars 2024 - 20:00",
        duration: "2h",
        price: "45€",
        available: 23,
        places: "Sections A-B"
    },
    {
        title: "Pièce: Le Misanthrope",
        date: "20 Mars 2024 - 19:30",
        duration: "1h45",
        price: "35€",
        available: 12,
        places: "Sections A-C"
    },
    {
        title: "Comédie Musicale",
        date: "25 Mars 2024 - 18:00",
        duration: "2h30",
        price: "55€",
        available: 5,
        places: "Section VIP"
    }
];

// Génération dynamique des événements
function renderEvents() {
    const container = document.getElementById('eventsContainer');
    
    events.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'event-card';
        
        eventCard.innerHTML = `
            <h2 class="event-title">${event.title}</h2>
            <p class="event-date">${event.date}</p>
            <div class="event-details">
                <p>Durée: ${event.duration}</p>
                <p>Prix: ${event.price}</p>
                <p>Places disponibles: ${event.available}</p>
                <p>Sections: ${event.places}</p>
            </div>
            <button class="buy-button">Acheter un billet</button>
        `;

        container.appendChild(eventCard);
    });
}

// Initialisation
window.onload = renderEvents;