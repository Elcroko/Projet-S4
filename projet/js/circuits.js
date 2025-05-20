document.addEventListener('DOMContentLoaded', function () {
    const circuitsContainer = document.querySelector('.circuits-container');
    const sortSelect = document.getElementById('sort-by');
    const form = document.getElementById('filters-form');

    // *** Tableau contenant tous les circuits ***
    let circuits = Array.from(document.querySelectorAll('.circuit-link'));

    // Fonction principale appliquant tri et filtres
    function applyFiltersAndSort() {
        const selectedEpoque = document.getElementById('epoque').value.toLowerCase();
        const selectedLieu = document.getElementById('lieu').value.toLowerCase();
        const selectedPrix = document.getElementById('prix').value;
        const sortBy = sortSelect.value;
    
        const allCircuits = Array.from(document.querySelectorAll('#js-voyages .circuit-link'));
        const container = document.querySelector('.circuits-container');
        const pagination = document.querySelector('.pagination');
    
        // Vérifie s'il y a un filtre ou tri actif
        const filtreActif = selectedEpoque || selectedLieu || selectedPrix || sortBy;
    
        // Si aucun filtre/tri, on n'agit pas : laisser le PHP afficher ses 6 voyages paginés
        if (!filtreActif) {
            const originalContainer = document.getElementById('original-circuits');
            const jsContainer = document.querySelector('.circuits-container');
            const pagination = document.querySelector('.pagination');
        
            if (originalContainer && jsContainer) {
                jsContainer.innerHTML = originalContainer.innerHTML;
                jsContainer.style.display = 'flex';
            }
            if (pagination) {
                pagination.style.display = '';
            }
            return;
        }        
    
        // Filtrage des circuits selon critères
        let filtered = allCircuits.filter(circuit => {
            const ep = (circuit.dataset.epoque || '').toLowerCase();
            const li = (circuit.dataset.lieu || '').toLowerCase();
            const pr = parseFloat(circuit.dataset.prix) || 0;
    
            // Application des filtres
            if (selectedEpoque && ep !== selectedEpoque) return false;
            if (selectedLieu && li !== selectedLieu) return false;
            if (selectedPrix === '1' && pr >= 1000) return false;
            if (selectedPrix === '2' && (pr < 1000 || pr > 2000)) return false;
            if (selectedPrix === '3' && pr <= 2000) return false;
    
            return true;
        });
    
        // Tri dynamique
        if (sortBy) {
            filtered.sort((a, b) => {
                const getValue = (el, type) => {
                    if (type === 'prix') return parseFloat(el.dataset.prix) || 0;
                    const texts = [...el.querySelectorAll('p')].map(p => p.textContent.toLowerCase());
                    if (type === 'duree') return parseInt(texts.find(t => t.includes('durée'))?.replace(/\D/g, '') || 0);
                    if (type === 'etapes') return parseInt(texts.find(t => t.includes('étapes'))?.replace(/\D/g, '') || 0);
                    return 0;
                };
                return getValue(a, sortBy) - getValue(b, sortBy);
            });
        }
    
        // Remplace affichage paginé par résultats dynamiques
        document.querySelector('.circuits-container').style.display = 'none';
        pagination.style.display = 'none';
    
        // On réinjecte les résultats filtrés/triés dans le conteneur
        const jsContainer = document.querySelector('.circuits-container');
        jsContainer.innerHTML = '';
        if (filtered.length === 0) {
            jsContainer.innerHTML = '<p class="no-result">Aucun circuit ne correspond à votre recherche.</p>';
        } else {
            filtered.forEach(c => jsContainer.appendChild(c.cloneNode(true)));
            jsContainer.style.display = 'flex'; 
        }

        const originalContainer = document.getElementById('original-circuits');
        // S'il n'y a aucun filtre actif, on réaffiche la version paginée PHP
        if (!selectedEpoque && !selectedLieu && !selectedPrix && !sortBy) {
            if (originalContainer) {
                circuitsContainer.innerHTML = originalContainer.innerHTML;
                pagination.style.display = '';
                return; // stop ici, on affiche le HTML original
            }
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
    sortSelect.addEventListener('change', applyFiltersAndSort);

    // Lancer au chargement
    applyFiltersAndSort();
});
