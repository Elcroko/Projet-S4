document.addEventListener('DOMContentLoaded', () => {
    const panierIcon = document.querySelector('.panier-wrapper');
    const panierDropdown = document.querySelector('.panier-dropdown');
    const badge = document.getElementById('panier-badge');

    if (panierIcon && panierDropdown) {
        panierIcon.addEventListener('mouseenter', () => {
            panierDropdown.classList.add('visible');
        });

        panierIcon.addEventListener('mouseleave', () => {
            panierDropdown.classList.remove('visible');
        });
    }

    document.addEventListener('click', function (e) {
        const target = e.target;
        if (target.classList.contains('supprimer-btn')) {
            e.preventDefault();
            const id = target.dataset.id;

            if (!id) return;

            if (!confirm("Voulez-vous vraiment supprimer ce voyage du panier ?")) return;

            fetch('supprimer_panier.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'supprimer_id=' + encodeURIComponent(id)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const item = target.closest('.item-panier');
                    if (item) item.remove();

                    // ✅ Mise à jour du compteur
                    if (badge) {
                        const count = document.querySelectorAll('.item-panier').length;
                        badge.textContent = count;
                    }

                    alert("✅ Voyage supprimé du panier.");
                } else {
                    alert("⚠ Erreur lors de la suppression du voyage.");
                }
            })
            .catch(() => {
                alert("❌ Erreur de communication avec le serveur.");
            });
        }
    });
});
