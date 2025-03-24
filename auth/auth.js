function validateLogin() {
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    // Validation basique
    if (!email.includes('@')) {
        alert("Email invalide !");
        return false;
    }

    // Ici vous ajouterez l'appel AJAX vers le backend PHP
    alert("Connexion réussie (simulation)");
    return false; // Empêche l'envoi du formulaire pour l'exemple
}



function validateRegister() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
        alert("Les mots de passe ne correspondent pas !");
        return false;
    }
    
    // Ici vous ajouterez l'appel AJAX vers le backend PHP
    alert("Inscription réussie (simulation)");
    return false; // Empêche l'envoi du formulaire pour l'exemple
}