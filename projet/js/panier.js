document.addEventListener('DOMContentLoaded', () => {
    const panierIcon = document.querySelector('.panier-wrapper');
    const panierDropdown = document.querySelector('.panier-dropdown');

    // Affiche le menu déroulant au survol
    panierIcon.addEventListener('mouseenter', () => {
        panierDropdown.classList.add('visible');
    });

    // Cache le menu déroulant quand on sort
    panierIcon.addEventListener('mouseleave', () => {
        panierDropdown.classList.remove('visible');
    });
});


