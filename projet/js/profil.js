document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-btn');
    const saveButton = document.getElementById('save-changes');
    const form = document.querySelector('.profil-form');
    const fields = {};

    editButtons.forEach(button => {
        const fieldId = button.getAttribute('data-field');
        const input = document.getElementById(fieldId);
        fields[fieldId] = input.value; // Stocke la valeur initiale

        button.addEventListener('click', function () {
            if (input.hasAttribute('readonly')) {
                input.removeAttribute('readonly');
                button.textContent = 'Annuler';
                input.focus();
            } else {
                input.setAttribute('readonly', true);
                input.value = fields[fieldId];
                button.textContent = 'Modifier';
            }
            updateSaveButton();
        });

        input.addEventListener('input', updateSaveButton);
    });

    function updateSaveButton() {
        let hasChanges = false;
        for (const fieldId in fields) {
            const input = document.getElementById(fieldId);
            if (!input.hasAttribute('readonly') && input.value !== fields[fieldId]) {
                hasChanges = true;
            }
        }
        saveButton.style.display = hasChanges ? 'block' : 'none';
    }

    form.addEventListener('submit', function (e) {
        if (!validateForm()) {
            e.preventDefault(); // Bloquer si les champs sont invalides
        }
    });

    function validateForm() {
        // Regex très stricts
        const letterRegex = /^[a-zà-öø-ÿ\s-]+$/i;  // lettres, accents, espaces, tirets
        const emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/; // email minuscule uniquement
        const phoneRegex = /^[0-9]{10}$/; // exactement 10 chiffres

        const nom = document.getElementById('nom').value.trim();
        const prenom = document.getElementById('prenom').value.trim();
        const email = document.getElementById('email').value.trim();
        const telephone = document.getElementById('telephone').value.trim();

        if (!letterRegex.test(nom)) {
            alert('Le nom doit contenir uniquement des lettres, espaces ou tirets.');
            return false;
        }

        if (!letterRegex.test(prenom)) {
            alert('Le prénom doit contenir uniquement des lettres, espaces ou tirets.');
            return false;
        }

        if (!emailRegex.test(email)) {
            alert('L\'email doit être entièrement en minuscules et valide.');
            return false;
        }

        if (!phoneRegex.test(telephone)) {
            alert('Le numéro de téléphone doit contenir exactement 10 chiffres.');
            return false;
        }

        return true; // Tous les champs sont bons ✅
    }
});
