document.addEventListener('DOMContentLoaded', () => {
    const panierIcon = document.querySelector('.panier-wrapper');
    const panierDropdown = document.querySelector('.panier-dropdown');

    if (panierIcon && panierDropdown) {
        panierIcon.addEventListener('mouseenter', () => {
            panierDropdown.classList.add('visible');
        });

        panierIcon.addEventListener('mouseleave', () => {
            panierDropdown.classList.remove('visible');
        });

        // Suppression AJAX
        document.querySelectorAll('.supprimer-btn').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.dataset.id;

                if (confirm("Êtes-vous sûr de vouloir supprimer ce voyage du panier ?")) {
                    fetch('supprimer_panier.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'supprimer_id=' + encodeURIComponent(id)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.item-panier').remove();
                        } else {
                            alert("Erreur : suppression impossible.");
                        }
                    })
                    .catch(() => alert("Erreur de requête AJAX"));
                }
            });
        });
    }
});
