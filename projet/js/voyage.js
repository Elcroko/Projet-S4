document.addEventListener('DOMContentLoaded', function () {
    const voyageId = document.body.getAttribute('data-voyage-id');
    const etapesWrapper = document.querySelector('.etapes-wrapper');
    const nombreInput = document.querySelector('input[name="nombre_personnes"]');
    const prixAfficheElement = document.querySelector('.prix-total strong');
    const prixBase = parseFloat(prixAfficheElement.getAttribute('data-prix-base')) || 0;

    let allSelects = []; // Pour garder une référence à tous les selects créés

    function recalculerPrix() {
        let totalCoutOptions = 0;
        allSelects.forEach(select => {
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.dataset.prix) {
                totalCoutOptions += parseFloat(selectedOption.dataset.prix);
            }
        });

        const nbVoyageurs = parseInt(nombreInput.value) || 1;
        const prixFinal = (prixBase + totalCoutOptions) * nbVoyageurs;

        prixAfficheElement.textContent = prixFinal.toLocaleString('fr-FR') + ' €';
    }

    function creerEtAfficherOptionsPourEtapes(optionsParCleEtape) {
        // Pour chaque placeholder d'étape dans le HTML
        document.querySelectorAll('.etapes-wrapper .step').forEach(stepDiv => {
            const cleEtape = stepDiv.getAttribute('data-etape-cle');
            const placeholder = stepDiv.querySelector('.options-placeholder');
            
            if (placeholder) {
                placeholder.innerHTML = ''; // Vider le spinner ou message précédent
            } else {
                console.error(`Placeholder non trouvé pour l'étape ${cleEtape}`);
                return; // Passer à l'étape suivante si le placeholder n'est pas là
            }

            if (optionsParCleEtape[cleEtape]) {
                const optionsPourCetteEtape = optionsParCleEtape[cleEtape]; // Objet de catégories
                
                Object.keys(optionsPourCetteEtape).forEach(categorie => {
                    const optionsDisponiblesPourCategorie = optionsPourCetteEtape[categorie]; // Objet d'options: {nom: prix}

                    const label = document.createElement('label');
                    label.textContent = categorie.charAt(0).toUpperCase() + categorie.slice(1) + ' :';
                    placeholder.appendChild(label);

                    const select = document.createElement('select');
                    // Le nom doit correspondre à la structure attendue par le PHP pour le POST:
                    // options[nom_etape_cle][index_fixe_pour_structure_actuelle][nom_categorie]
                    // Votre PHP original utilisait un index [0] comme options[etapeX][0][categorie]
                    select.name = `options[${cleEtape}][0][${categorie}]`;

                    Object.keys(optionsDisponiblesPourCategorie).forEach(nomOption => {
                        const prixOption = optionsDisponiblesPourCategorie[nomOption];
                        const optionElement = document.createElement('option');
                        optionElement.value = nomOption; // La valeur textuelle de l'option
                        optionElement.textContent = `${nomOption} ${prixOption > 0 ? '(+' + prixOption + '€)' : ''}`;
                        optionElement.dataset.prix = prixOption; // Stocker le prix
                        select.appendChild(optionElement);
                    });
                    
                    select.addEventListener('change', recalculerPrix);
                    placeholder.appendChild(select);
                    allSelects.push(select); // Ajouter à la liste pour recalculerPrix
                });
            } else {
                placeholder.innerHTML = "<p>Aucune option configurable pour cette étape.</p>";
            }
        });
        
        recalculerPrix(); // Recalculer le prix après avoir ajouté toutes les options
    }

    if (voyageId && etapesWrapper) {
        // Afficher les spinners pendant le chargement initial (déjà fait via HTML/CSS)
        fetch(`voyage.php?action=get_options&voyage_id_ajax=${voyageId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status} - ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error("Erreur lors de la récupération des options:", data.error);
                    document.querySelectorAll('.options-placeholder').forEach(ph => {
                        ph.innerHTML = `<p style="color:red;">Impossible de charger les options.</p>`;
                    });
                } else if (Object.keys(data).length === 0) {
                     document.querySelectorAll('.options-placeholder').forEach(ph => {
                        ph.innerHTML = `<p>Aucune option personnalisable pour ce voyage.</p>`;
                    });
                }
                else {
                    creerEtAfficherOptionsPourEtapes(data);
                }
            })
            .catch(error => {
                console.error("Erreur lors du fetch des options:", error);
                 document.querySelectorAll('.options-placeholder').forEach(ph => {
                    ph.innerHTML = `<p style="color:red;">Erreur de communication (voir console).</p>`;
                });
            });
    } else {
        if (!voyageId) console.error("ID du voyage non trouvé sur la page (attribut data-voyage-id sur body).");
        if (!etapesWrapper) console.error("Conteneur '.etapes-wrapper' non trouvé.");
        document.querySelectorAll('.options-placeholder').forEach(ph => {
            ph.innerHTML = `<p style="color:red;">Erreur de configuration de la page.</p>`;
        });
    }

    if (nombreInput) {
        nombreInput.addEventListener('input', recalculerPrix);
    }
});
