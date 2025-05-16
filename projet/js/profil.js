document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-btn');
    const saveButton = document.getElementById('save-changes');
    const form = document.getElementById('profil-form');
    const fieldsInitialValues = {}; // Pour stocker les valeurs initiales des champs éditables
    const messageArea = document.getElementById('profil-message-area');

    function displayMessage(message, type) {
        messageArea.textContent = message;
        messageArea.className = 'profil-message ' + type; // 'success' or 'error'
    }

    editButtons.forEach(button => {
        const fieldId = button.getAttribute('data-field');
        const input = document.getElementById(fieldId);
        
        // Stocker la valeur initiale au chargement de la page
        fieldsInitialValues[fieldId] = input.value;

        button.addEventListener('click', function () {
            if (input.hasAttribute('readonly')) {
                input.removeAttribute('readonly');
                button.textContent = 'Annuler';
                input.focus();
            } else {
                input.setAttribute('readonly', true);
                input.value = fieldsInitialValues[fieldId]; // Restaurer la valeur initiale en cas d'annulation
                button.textContent = 'Modifier';
            }
            updateSaveButtonVisibility();
        });

        input.addEventListener('input', updateSaveButtonVisibility);
    });

    function updateSaveButtonVisibility() {
        let hasChanges = false;
        for (const fieldId in fieldsInitialValues) {
            const input = document.getElementById(fieldId);
            if (!input.hasAttribute('readonly') && input.value !== fieldsInitialValues[fieldId]) {
                hasChanges = true;
                break;
            }
        }
        saveButton.style.display = hasChanges ? 'block' : 'none';
    }

    function validateForm() {
        const letterRegex = /^[a-zA-ZÀ-ÿ\s-]+$/i;
        const emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/; // Email en minuscules
        const phoneRegex = /^[0-9]{10}$/;

        const nomInput = document.getElementById('nom');
        const prenomInput = document.getElementById('prenom');
        const emailInput = document.getElementById('email');
        const telephoneInput = document.getElementById('telephone');
        
        let errors = [];

        if (!nomInput.hasAttribute('readonly') && !letterRegex.test(nomInput.value.trim())) {
            errors.push('Le nom doit contenir uniquement des lettres, espaces ou tirets.');
        }
        if (!prenomInput.hasAttribute('readonly') && !letterRegex.test(prenomInput.value.trim())) {
            errors.push('Le prénom doit contenir uniquement des lettres, espaces ou tirets.');
        }
        if (!emailInput.hasAttribute('readonly') && !emailRegex.test(emailInput.value.trim())) {
            errors.push("L'email doit être valide et en minuscules (ex: test@example.com).");
        }
        if (!telephoneInput.hasAttribute('readonly') && !phoneRegex.test(telephoneInput.value.trim())) {
            errors.push('Le numéro de téléphone doit contenir exactement 10 chiffres.');
        }

        if (errors.length > 0) {
            displayMessage(errors.join('\n'), 'error');
            return false;
        }
        return true;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Empêcher la soumission traditionnelle du formulaire

        if (!validateForm()) {
            return; // Arrêter si la validation côté client échoue
        }
        
        displayMessage('', ''); // Effacer les messages précédents
        saveButton.textContent = 'Sauvegarde...';
        saveButton.disabled = true;

        const formData = new FormData(form);

        fetch('profil.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Indiquer que c'est une requête AJAX
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessage(data.message || 'Profil mis à jour avec succès!', 'success');
                // Mettre à jour les valeurs initiales stockées et les champs avec les nouvelles données
                if (data.user) {
                    Object.keys(data.user).forEach(fieldId => {
                        const input = document.getElementById(fieldId);
                        if (input) {
                            input.value = data.user[fieldId];
                            fieldsInitialValues[fieldId] = data.user[fieldId]; // Mettre à jour la valeur "originale"
                            input.setAttribute('readonly', true);
                            const editBtn = document.querySelector(`.edit-btn[data-field="${fieldId}"]`);
                            if (editBtn) editBtn.textContent = 'Modifier';
                        }
                    });
                }
                updateSaveButtonVisibility(); // Devrait cacher le bouton "Enregistrer"
            } else {
                displayMessage(data.message || 'Erreur lors de la mise à jour du profil.', 'error');
                if (data.redirect) { // Si l'authentification est requise
                    setTimeout(() => { window.location.href = data.redirect; }, 2000);
                }
                // Ne pas restaurer les champs si l'erreur est une erreur de validation serveur, 
                // l'utilisateur pourrait vouloir corriger.
                // Si c'est une autre erreur serveur, on pourrait envisager de restaurer.
            }
        })
        .catch(error => {
            console.error('Erreur Fetch:', error);
            displayMessage('Erreur de communication avec le serveur.', 'error');
        })
        .finally(() => {
            saveButton.textContent = 'Enregistrer les modifications';
            saveButton.disabled = false;
            // S'assurer que updateSaveButtonVisibility est appelé si nécessaire,
            // par exemple, si des champs sont restés éditables après une erreur serveur.
            updateSaveButtonVisibility(); 
        });
    });

    // Initialiser la visibilité du bouton "Enregistrer" au chargement
    updateSaveButtonVisibility();
});
