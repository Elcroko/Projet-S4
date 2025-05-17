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

                ['position', 'activite', 'hebergement', 'restauration', 'transport'].forEach(categorie => {
                    if (etape[categorie] && etape[categorie].length > 0) {
                        const label = document.createElement('label');
                        label.textContent = `${categorie.charAt(0).toUpperCase() + categorie.slice(1)} :`;
                        stepDiv.appendChild(label);

                        const select = document.createElement('select');
                        select.name = `options[etape${index + 1}][0][${categorie}]`;
                        select.classList.add('option-select');

                        etape[categorie].forEach(option => {
                            const opt = document.createElement('option');
                            opt.value = option.valeur;
                            opt.textContent = `${option.nom} ${option.prix > 0 ? '(+' + option.prix + '€)' : ''}`;
                            select.appendChild(opt);
                        });

                        stepDiv.appendChild(select);
                    }
                });

                container.appendChild(stepDiv);
            });

            // Ajout des events pour recalculer le prix
            document.querySelectorAll('.etapes-wrapper select').forEach(select => {
                select.addEventListener('change', calculerPrix);
            });

            calculerPrix();
        });

    function calculerPrix() {
        let total = basePrix;
        document.querySelectorAll('.etapes-wrapper select').forEach(select => {
            const selected = select.options[select.selectedIndex];
            const match = selected.textContent.match(/\+(\d+)/);
            if (match) total += parseFloat(match[1]);
        });
        const nb = parseInt(nombreInput.value) || 1;
        total *= nb;
        prixAffiche.textContent = total.toLocaleString('fr-FR') + ' €';
    }

    nombreInput.addEventListener('input', calculerPrix);
});
