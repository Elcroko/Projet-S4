document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-connexion');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('toggle-password');

    // Création d'un conteneur d’erreurs par champ
    const emailError = document.createElement('div');
    emailError.classList.add('error-message');
    emailInput.parentElement.appendChild(emailError);

    const passwordError = document.createElement('div');
    passwordError.classList.add('error-message');
    passwordInput.parentElement.appendChild(passwordError);

    // Bouton œil
    togglePassword.addEventListener('click', function () {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        this.textContent = type === 'text' ? '🙈' : '👁️';
    });

    // Validation du formulaire
    form.addEventListener('submit', function (e) {
        let valid = true;

        emailError.textContent = '';
        passwordError.textContent = '';

        if (!validateEmail(emailInput.value)) {
            valid = false;
            emailError.textContent = 'Adresse email invalide.';
        }

        if (!valid) e.preventDefault();
    });

    function validateEmail(email) {
        const re = /\S+@\S+\.\S+/;
        return re.test(email);
    }
});
