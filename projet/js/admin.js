document.addEventListener('DOMContentLoaded', function () {
    const adminButtons = document.querySelectorAll('.rendre-admin-btn');

    adminButtons.forEach(button => {
        // Lorsqu'on clique sur un bouton "rendre admin"
        button.addEventListener('click', function () {
            // Trouver la ligne de tableau correspondante
            const row = button.closest('tr');
            const adminCell = row.querySelector('.admin-status');
            const email = button.dataset.email;

            // Changer état du bouton pendant la requête
            button.disabled = true;
            button.textContent = "⏳";
            button.classList.add('loading');

            // Vérifie l'état actuel (admin ou non) pour basculer
            const currentAdmin = adminCell.textContent.trim().toLowerCase() === "oui";
            const newAdminStatus = currentAdmin ? 0 : 1;

            // Envoie de la nouvelle valeur au serveur
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'email=' + encodeURIComponent(email) + '&admin=' + encodeURIComponent(newAdminStatus)
            })
            .then(response => response.json())
            .then(data => {
                setTimeout(() => {
                    button.disabled = false;
                    button.classList.remove('loading');

                    if (data.success) {
                        // Mise à jour visuelle du statut et bouton
                        adminCell.textContent = newAdminStatus === 1 ? "Oui" : "Non";
                        button.textContent = newAdminStatus === 1 ? "Retirer admin" : "Rendre admin";
                        button.dataset.admin = newAdminStatus;

                        // Si nouvel admin, désactiver bannissement
                        const banButton = row.querySelector('.bannir-btn');
                        if (banButton) {
                            if (newAdminStatus === 1) {
                                banButton.disabled = true;
                                banButton.classList.add('disabled-btn');
                                banButton.textContent = "Admin protégé";
                                banButton.removeAttribute('data-email');
                                banButton.title = "Impossible de bannir un admin";
                            } else {
                                banButton.disabled = false;
                                banButton.classList.remove('disabled-btn');
                                banButton.dataset.email = email;
                                banButton.textContent = "Bannir";
                                banButton.title = "";
                            }
                        }
                    } else {
                        alert(data.message);
                        button.textContent = currentAdmin ? "Retirer admin" : "Rendre admin";
                    }
                }, 3000); // Délai de 3 secondes
            })
            .catch(error => {
                console.error('Erreur serveur (admin):', error);
                button.textContent = "❌ Erreur";
                button.disabled = false;
                button.classList.remove('loading');
            });
        });
    });

    // Sélectionne tous les boutons "bannir"
    const banButtons = document.querySelectorAll('.bannir-btn');

    banButtons.forEach(button => {
        button.addEventListener('click', function () {
            // On récupère la ligne de tableau de l'utilisateur concerné
            const row = button.closest('tr');
            const banCell = row.querySelector('.ban-status'); // cellule contenant "Oui"/"Non"
            const email = button.dataset.email;

            // Vérifie si l'utilisateur est actuellement banni
            const isCurrentlyBanned = banCell.textContent.trim().toLowerCase() === "oui";
            const newBanStatus = isCurrentlyBanned ? 0 : 1; // toggle : si oui → non, si non → oui

            // Affiche état de chargement sur le bouton
            button.disabled = true;
            button.textContent = "⏳";
            button.classList.add('loading');

            // Requête POST vers le script PHP
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'email=' + encodeURIComponent(email) + '&banni=' + encodeURIComponent(newBanStatus)
            })
            .then(response => response.json())
            .then(data => {
                setTimeout(() => {
                    button.disabled = false;
                    button.classList.remove('loading');

                    if (data.success) {
                        // Met à jour l'état visuel "banni"/"non banni"
                        banCell.textContent = newBanStatus === 1 ? "Oui" : "Non";
                        button.textContent = newBanStatus === 1 ? "Débannir" : "Bannir";
                        button.dataset.banni = newBanStatus;
                        const adminButton = row.querySelector('.rendre-admin-btn');
                        const adminCell = row.querySelector('.admin-status');

                        if (newBanStatus === 1) {
                            // Si l'utilisateur vient d'être banni :
                            // on désactive son bouton "rendre admin"
                            if (adminButton) {
                                adminButton.disabled = true;
                                adminButton.classList.add('disabled-btn');
                                adminButton.title = "Impossible de rendre admin un utilisateur banni";
                                adminButton.textContent = "Accès retiré";
                            }
                            if (adminCell) {
                                // Et on indique "Non" dans la colonne Admin
                                adminCell.textContent = "Non";
                            }
                        } else {
                            // Si l'utilisateur vient d'être débanni :
                            // on réactive son bouton "rendre admin"
                            if (adminButton) {
                                adminButton.disabled = false;
                                adminButton.classList.remove('disabled-btn');
                                adminButton.title = "";
                                adminButton.textContent = "Rendre admin";
                            }
                        }

                    } else {
                        // Si le serveur renvoie une erreur, on restaure l’état du bouton
                        alert(data.message);
                        button.textContent = isCurrentlyBanned ? "Débannir" : "Bannir";
                    }
                }, 3000); // Délai de 3 secondes
            })
            .catch(error => {
                // Erreur de communication avec le serveur
                console.error('Erreur serveur (ban):', error);
                button.textContent = "❌ Erreur";
                button.disabled = false;
                button.classList.remove('loading');
            });
        });
    });
});
