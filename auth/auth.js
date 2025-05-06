function validateLogin() {
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    if (!email.includes('@')) {
        alert("Email invalide !");
        return false;
    }

    return true;
}

function validateRegister() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
        alert("Les mots de passe ne correspondent pas !");
        return false;
    }



    return true;
}