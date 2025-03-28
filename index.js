// Données des événements avec images
const events = [
    {
        title: "Concert Symphonique",
        date: "15 Mars 2024 - 20:00",
        duration: "2h",
        price: "45€",
        available: 23,
        places: "Sections A-B",
        image: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
        category: "Musique"
    },
    {
        title: "Pièce: Le Misanthrope",
        date: "20 Mars 2024 - 19:30",
        duration: "1h45",
        price: "35€",
        available: 12,
        places: "Sections A-C",
        image: "https://images.unsplash.com/photo-1547153760-18fc86324498?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
        category: "Théâtre"
    },
    {
        title: "Comédie Musicale",
        date: "25 Mars 2024 - 18:00",
        duration: "2h30",
        price: "55€",
        available: 5,
        places: "Section VIP",
        image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTteuLlvkTx6pnytYLTbtE3mD2CQgdOzJW4sQ&s",
        category: "Comédie"
    },
    {
        title: "Ballet Classique",
        date: "2 Avril 2024 - 19:00",
        duration: "2h15",
        price: "50€",
        available: 8,
        places: "Sections A-D",
        image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR9Kixg_37kSah2iJmo_JUsY-s1AEUzIq5HZw&s",
        category: "Danse"
    },
    {
        title: "Opéra: La Traviata",
        date: "10 Avril 2024 - 20:30",
        duration: "3h",
        price: "60€",
        available: 3,
        places: "Section VIP",
        image: "https://images.unsplash.com/photo-1501612780327-45045538702b?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
        category: "Opéra"
    },
    // {
    //     title: "Spectacle de Magie",
    //     date: "18 Avril 2024 - 17:00",
    //     duration: "1h30",
    //     price: "30€",
    //     available: 15,
    //     places: "Sections B-C",
    //     image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT3kkLHoH2gQGKgorZiP2sizZp6AhNa4lFG-Q&s",
    //     category: "Magie"
    // }
];

// Génération dynamique des événements
function renderEvents() {
    const container = document.getElementById('eventsContainer');

    events.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'event-card';

        // Déterminer la classe de disponibilité
        let availabilityClass = '';
        if (event.available <= 5) {
            availabilityClass = 'low-availability';
        } else if (event.available === 0) {
            availabilityClass = 'sold-out';
        }

        eventCard.innerHTML = `
            <img src="${event.image}" alt="${event.title}" class="event-image">
            <div class="event-content">
                <h2 class="event-title">${event.title}</h2>
                <p class="event-date">${event.date}</p>
                <div class="event-details">
                    <p>Durée: ${event.duration}</p>
                    <p>Prix: ${event.price}</p>
                    <p>Sections: ${event.places}</p>
                    <p>Catégorie: ${event.category}</p>
                </div>
                
                <button class="buy-button" onclick="window.location.href='./Reservation/reservation.html'">
                    ${event.available > 0 ? 'Acheter un billet' : 'Voir détails'}
                </button>
            </div>
        `;

        container.appendChild(eventCard);
    });
}

window.onload = renderEvents;