document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const nom = document.querySelector('#nom');
    const prenom = document.querySelector('#prenom');
    const email = document.querySelector('#email');
    const motDePasse = document.querySelector('#mot_de_passe');
    const confirmMdp = document.querySelector('#confirm_mdp');
    const dateNaissance = document.querySelector('input[name="date_naissance"]');
    const telephone = document.querySelector('#telephone');
    const nomCount = document.querySelector('#nom-count');
    const prenomCount = document.querySelector('#prenom-count');
    const mdpCount = document.querySelector('#mdp-count');
    const eyeIcons = document.querySelectorAll('.toggle-password');

    const showError = (input, message) => {
        let error = input.parentElement.querySelector('.error-message');
        if (!error) {
            error = document.createElement('div');
            error.classList.add('error-message');
            input.parentElement.appendChild(error);
        }
        error.textContent = message;
    };

    const clearError = (input) => {
        const error = input.parentElement.querySelector('.error-message');
        if (error) error.textContent = '';
    };

    const updateCount = (input, countElement, max = 30) => {
        countElement.textContent = `${input.value.length}/${max}`;
    };

    nom.addEventListener('input', () => updateCount(nom, nomCount));
    prenom.addEventListener('input', () => updateCount(prenom, prenomCount));
    motDePasse.addEventListener('input', () => updateCount(motDePasse, mdpCount, 20));

    eyeIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const input = this.previousElementSibling;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? '🙈' : '👁️';
        });
    });

    form.addEventListener('submit', (e) => {
        let valid = true;

        if (nom.value.length < 2) {
            showError(nom, 'Nom trop court');
            valid = false;
        } else {
            clearError(nom);
        }

        if (prenom.value.length < 2) {
            showError(prenom, 'Prénom trop court');
            valid = false;
        } else {
            clearError(prenom);
        }

        if (!email.value.includes('@') || email.value.trim() === '') {
            showError(email, 'Email invalide');
            valid = false;
        } else {
            clearError(email);
        }

        if (motDePasse.value.length < 6) {
            showError(motDePasse, 'Mot de passe trop court (min 6 caractères)');
            valid = false;
        } else {
            clearError(motDePasse);
        }

        if (confirmMdp.value !== motDePasse.value) {
            showError(confirmMdp, 'Les mots de passe ne correspondent pas');
            valid = false;
        } else {
            clearError(confirmMdp);
        }

        if (!/^0[1-9](\d{2}){4}$/.test(telephone.value)) {
            showError(telephone, 'Numéro de téléphone invalide (format attendu : 0612345678)');
            valid = false;
        } else {
            clearError(telephone);
        }

        // Vérification de l'âge
        if (dateNaissance.value) {
            const birthDate = new Date(dateNaissance.value);
            const today = new Date();
            const age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            const dayDiff = today.getDate() - birthDate.getDate();

            const is18 =
                age > 18 ||
                (age === 18 && (monthDiff > 0 || (monthDiff === 0 && dayDiff >= 0)));

            if (!is18) {
                showError(dateNaissance, 'Vous devez avoir au moins 18 ans');
                valid = false;
            } else {
                clearError(dateNaissance);
            }
        } else {
            showError(dateNaissance, 'Veuillez entrer votre date de naissance');
            valid = false;
        }

        if (!document.querySelector('#terms').checked) {
            alert("Vous devez accepter les termes et conditions.");
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
});
