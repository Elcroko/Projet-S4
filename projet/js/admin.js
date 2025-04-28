document.addEventListener('DOMContentLoaded', function () {
    const adminButtons = document.querySelectorAll('.admin-btn');

    adminButtons.forEach(button => {
        button.addEventListener('click', function () {
            const row = button.closest('tr');
            const adminCell = row.querySelector('.admin-status');
            const email = button.dataset.email;

            // Désactiver bouton
            button.disabled = true;
            button.textContent = "⏳";
            button.classList.add('loading');

            // Simuler attente
            setTimeout(() => {
                let newAdminStatus;

                if (adminCell.textContent.trim().toLowerCase() === "oui") {
                    adminCell.textContent = "Non";
                    button.textContent = "Rendre admin";
                    newAdminStatus = 0;
                } else {
                    adminCell.textContent = "Oui";
                    button.textContent = "Retirer admin";
                    newAdminStatus = 1;
                }

                button.dataset.admin = newAdminStatus;

                button.disabled = false;
                button.classList.remove('loading');

                // Envoyer AJAX avec email ET nouveau statut
                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=' + encodeURIComponent(email) + '&admin=' + encodeURIComponent(newAdminStatus)
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Serveur :', data);
                })
                .catch(error => {
                    console.error('Erreur serveur :', error);
                });
            }, 3000);
        });
    });
});
