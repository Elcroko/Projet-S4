document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.etapes-wrapper');
    const nombreInput = document.querySelector('input[name="nombre_personnes"]');
    const prixAffiche = document.querySelector('.prix-total strong');
    let basePrix = parseFloat(prixAffiche.textContent.replace(/\s|€/g, '')) || 0;

    const params = new URLSearchParams(window.location.search);
    const id = params.get('id') || 'voyage01';

    fetch('voyage.php?options=' + encodeURIComponent(id))
        .then(res => res.json())
        .then(data => {
            container.innerHTML = '';

            data.etapes.forEach((etape, index) => {
                const stepDiv = document.createElement('div');
                stepDiv.className = 'step';

                const title = document.createElement('h4');
                title.textContent = `Étape ${index + 1}`;
                stepDiv.appendChild(title);
                
                // Pour chaque catégorie (hébergement, transport, etc.)
                ['position', 'activite', 'hebergement', 'restauration', 'transport'].forEach(categorie => {
                    if (etape[categorie] && etape[categorie].length > 0) {
                        const label = document.createElement('label');
                        label.textContent = `${categorie.charAt(0).toUpperCase() + categorie.slice(1)} :`;
                        stepDiv.appendChild(label);

                        const select = document.createElement('select');
                        select.name = `options[etape${index + 1}][0][${categorie}]`;
                        select.classList.add('option-select');
                        select.dataset.etape = `etape${index + 1}`;
                        select.dataset.categorie = categorie;

                        // Ajoute les choix disponibles
                        etape[categorie].forEach(option => {
                            const opt = document.createElement('option');
                            opt.value = option.valeur;
                            opt.textContent = `${option.nom} ${option.prix > 0 ? '(+' + option.prix + '€)' : ''}`;
                            select.appendChild(opt);
                        });

                        stepDiv.appendChild(select);
                        // Recalcul du prix si changement
                        select.addEventListener('change', sendPrixRequest); 
                    }
                });

                container.appendChild(stepDiv);
            });

            // Réagit aussi à un changement du nombre de personnes
            nombreInput.addEventListener('input', sendPrixRequest);
            sendPrixRequest(); // premier calcul
        });

    function sendPrixRequest() {
        const selections = {};
        document.querySelectorAll('.etapes-wrapper select').forEach(select => {
            const etape = select.dataset.etape;
            const cat = select.dataset.categorie;
            const val = select.value;
            if (!selections[etape]) selections[etape] = [{}];
            selections[etape][0][cat] = val;
        });

        const nb = parseInt(nombreInput.value) || 1;

        fetch('voyage.php?calcul_prix=1', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: new URLSearchParams(window.location.search).get('id') || 'voyage01',
                nombre: nb,
                options: selections
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                prixAffiche.textContent = data.prix_total.toLocaleString('fr-FR') + ' €';
            } else {
                prixAffiche.textContent = 'Erreur';
            }
        });
    }
});
