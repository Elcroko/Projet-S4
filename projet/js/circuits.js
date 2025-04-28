document.addEventListener('DOMContentLoaded', function () {
    const circuitsContainer = document.querySelector('.circuits-container');
    const sortSelect = document.getElementById('sort-by');
    const form = document.getElementById('filters-form');
    const filterButton = form.querySelector('button.btn');

    // *** Tableau contenant tous les circuits ***
    let circuits = Array.from(document.querySelectorAll('.circuit-link'));

    function applyFiltersAndSort() {
        const selectedEpoque = document.getElementById('epoque').value.toLowerCase();
        const selectedLieu = document.getElementById('lieu').value.toLowerCase();
        const selectedPrix = document.getElementById('prix').value;
        const sortBy = sortSelect.value;

        // 1. Filtrage
        let filteredCircuits = circuits.filter(circuit => {
            const circuitEpoque = (circuit.dataset.epoque || '').toLowerCase();
            const circuitLieu = (circuit.dataset.lieu || '').toLowerCase();
            const circuitPrix = parseFloat(circuit.dataset.prix) || 0;

            if (selectedEpoque && circuitEpoque !== selectedEpoque) return false;
            if (selectedLieu && circuitLieu !== selectedLieu) return false;
            if (selectedPrix) {
                if (selectedPrix === '1' && circuitPrix >= 1000) return false;
                if (selectedPrix === '2' && (circuitPrix < 1000 || circuitPrix > 2000)) return false;
                if (selectedPrix === '3' && circuitPrix <= 2000) return false;
            }
            return true;
        });

        // 2. Tri
        if (sortBy) {
            filteredCircuits.sort((a, b) => {
                const aValue = extractValue(a, sortBy);
                const bValue = extractValue(b, sortBy);
                return aValue - bValue;
            });
        }

        // 3. Affichage
        circuitsContainer.innerHTML = '';
        if (filteredCircuits.length === 0) {
            circuitsContainer.innerHTML = '<p class="no-result">Aucun circuit ne correspond à votre recherche.</p>';
        } else {
            filteredCircuits.forEach(circuit => circuitsContainer.appendChild(circuit));
        }
    }

    function extractValue(circuit, type) {
        switch (type) {
            case 'prix':
                const prixText = [...circuit.querySelectorAll('p')].find(p => p.textContent.includes('A partir de'))?.textContent || '';
                return parseFloat(prixText.replace(/[^\d]/g, '')) || 0;
            case 'duree':
                const dureeText = [...circuit.querySelectorAll('p')].find(p => p.textContent.includes('Durée'))?.textContent || '';
                return parseInt(dureeText.replace(/[^\d]/g, '')) || 0;
            case 'etapes':
                const etapesText = [...circuit.querySelectorAll('p')].find(p => p.textContent.includes('Nombre d’étapes'))?.textContent || '';
                return parseInt(etapesText.replace(/[^\d]/g, '')) || 0;
            default:
                return 0;
        }
    }

    // Lancer au changement de filtres
    form.addEventListener('change', applyFiltersAndSort);
    filterButton.addEventListener('click', applyFiltersAndSort);
    sortSelect.addEventListener('change', applyFiltersAndSort);

    // Lancer au chargement
    applyFiltersAndSort();
});
