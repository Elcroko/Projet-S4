document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-connexion');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordCounter = document.getElementById('password-counter');
    const togglePassword = document.getElementById('toggle-password');

    // Compteur de caractères pour mot de passe
    passwordInput.addEventListener('input', function() {
        passwordCounter.textContent = `${passwordInput.value.length} caractères`;
    });

    // Bouton œil pour cacher/montrer le mot de passe
    togglePassword.addEventListener('click', function () {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
    });

    // Validation du formulaire
    form.addEventListener('submit', function (e) {
        let valid = true;
        let messages = [];

        if (!validateEmail(emailInput.value)) {
            valid = false;
            messages.push('Adresse email invalide.');
        }

        if (passwordInput.value.length < 8) {
            valid = false;
            messages.push('Mot de passe trop court (8 caractères minimum).');
        }

        if (!valid) {
            e.preventDefault();
            alert(messages.join('\n'));
        }
    });

    function validateEmail(email) {
        const re = /\S+@\S+\.\S+/;
        return re.test(email);
    }
});
