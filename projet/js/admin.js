document.addEventListener('DOMContentLoaded', function () {
    const adminButtons = document.querySelectorAll('.rendre-admin-btn');

    adminButtons.forEach(button => {
        button.addEventListener('click', function () {
            const row = button.closest('tr');
            const adminCell = row.querySelector('.admin-status');
            const email = button.dataset.email;

            button.disabled = true;
            button.textContent = "⏳";
            button.classList.add('loading');

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

                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=' + encodeURIComponent(email) + '&admin=' + encodeURIComponent(newAdminStatus)
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Serveur (admin):', data);
                })
                .catch(error => {
                    console.error('Erreur serveur (admin):', error);
                });
            }, 3000);
        });
    });

    const banButtons = document.querySelectorAll('.bannir-btn');

    banButtons.forEach(button => {
        button.addEventListener('click', function () {
            const row = button.closest('tr');
            const banCell = row.querySelector('.ban-status');
            const email = button.dataset.email;

            button.disabled = true;
            button.textContent = "⏳";
            button.classList.add('loading');

            setTimeout(() => {
                let newBanStatus;

                if (banCell.textContent.trim().toLowerCase() === "oui") {
                    banCell.textContent = "Non";
                    button.textContent = "Bannir";
                    newBanStatus = 0;
                } else {
                    banCell.textContent = "Oui";
                    button.textContent = "Débannir";
                    newBanStatus = 1;
                }

                button.dataset.banni = newBanStatus;
                button.disabled = false;
                button.classList.remove('loading');

                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=' + encodeURIComponent(email) + '&banni=' + encodeURIComponent(newBanStatus)
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Serveur (ban):', data);
                })
                .catch(error => {
                    console.error('Erreur serveur (ban):', error);
                });
            }, 3000);
        });
    });
});
