document.addEventListener('DOMContentLoaded', function () {
    const selects = document.querySelectorAll('.etapes-wrapper select');
    const nombreInput = document.querySelector('input[name="nombre_personnes"]');
    const prixAffiche = document.querySelector('.prix-total strong');
    const basePrix = parseFloat(prixAffiche?.textContent?.replace(/\s|€/g, '')) || 0;

    function recalculerPrix() {
        let total = basePrix;

        // Parcours des <select>
        selects.forEach(select => {
            const selectedOption = select.options[select.selectedIndex];
            const text = selectedOption.textContent;
            const match = text.match(/\+(\d+)\s?€/);
            if (match) {
                total += parseFloat(match[1]);
            }
        });

        // Multiplication par le nombre de voyageurs
        const nb = parseInt(nombreInput.value) || 1;
        total *= nb;

        prixAffiche.textContent = total.toLocaleString('fr-FR') + ' €';
    }

    // Ajout des écouteurs
    selects.forEach(select => select.addEventListener('change', recalculerPrix));
    nombreInput.addEventListener('input', recalculerPrix);
});
