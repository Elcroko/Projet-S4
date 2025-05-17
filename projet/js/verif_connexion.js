document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-connexion');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('toggle-password');

    // Conteneurs d'erreurs JS (vous les avez peut-être déjà)
    const emailJsError = document.getElementById('email-js-error'); // Assurez-vous que cet ID existe dans votre HTML
    const passwordJsError = document.getElementById('password-js-error'); // Assurez-vous que cet ID existe

    // Ciblez les conteneurs d'erreurs PHP
    const emailPhpError = document.getElementById('email-php-error');
    const passwordPhpError = document.getElementById('password-php-error');

    // Si un message d'erreur PHP existe pour le mot de passe,
    // et qu'il n'y a pas d'erreur PHP pour l'email (ou que l'email a une valeur retenue),
    // alors on efface le champ mot de passe.
    if (passwordPhpError && passwordPhpError.textContent.trim() !== "") {
        if (!emailPhpError || emailPhpError.textContent.trim() === "" || emailInput.value !== "") {
            passwordInput.value = ""; // Efface le mot de passe
        }
    }

    // Bouton œil pour le mot de passe
    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            this.textContent = type === 'text' ? '🙈' : '👁️';
        });
    }

    // Validation du formulaire côté client (avant soumission)
    if (form) {
        form.addEventListener('submit', function (e) {
            let valid = true;

            // Effacer les messages d'erreur JS précédents
            if(emailJsError) emailJsError.textContent = '';
            if(passwordJsError) passwordJsError.textContent = '';

            // Validation de l'email
            if (!validateEmail(emailInput.value)) {
                valid = false;
                if(emailJsError) emailJsError.textContent = 'Adresse email invalide.';
            }

            // Validation du mot de passe (simple vérification non vide ici, vous pouvez ajouter plus)
            if (passwordInput.value.trim() === '') {
                valid = false;
                if(passwordJsError) passwordJsError.textContent = 'Le mot de passe est requis.';
            }

            if (!valid) {
                e.preventDefault(); // Empêche la soumission du formulaire si invalide
            }
        });
    }

    function validateEmail(email) {
        // Expression régulière simple pour la validation d'email
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }
});
