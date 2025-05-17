<?php
require_once 'verif_banni.php'; // Assurez-vous que session_start() est appelé ici ou au début de verif_banni.php

$id_page_voyage = isset($_GET['id']) ? basename($_GET['id']) : 'voyage01'; // ID du voyage pour l'affichage de la page

// --- DÉBUT Gestion des requêtes AJAX pour les options ---
if (isset($_GET['action']) && $_GET['action'] === 'get_options' && isset($_GET['voyage_id_ajax'])) {
    $voyage_id_req_ajax = basename($_GET['voyage_id_ajax']);
    $nom_fichier_options_ajax = "json/{$voyage_id_req_ajax}.json";

    if (!file_exists($nom_fichier_options_ajax)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Fichier voyage non trouvé pour les options.']);
        exit;
    }

    $data_voyage_ajax = json_decode(file_get_contents($nom_fichier_options_ajax), true);
    $options_a_retourner_ajax = [];

    if ($data_voyage_ajax) {
        foreach ($data_voyage_ajax as $cle_etape => $valeur_etape) {
            // On s'attend à ce que $valeur_etape soit un tableau et que les options soient dans le premier élément.
            // Exemple: $data_voyage_ajax['etape1'][0]['options_disponibles']
            if (preg_match('/^etape\d+$/', $cle_etape) && isset($valeur_etape[0]['options_disponibles'])) {
                $options_a_retourner_ajax[$cle_etape] = $valeur_etape[0]['options_disponibles'];
            }
        }
        header('Content-Type: application/json');
        echo json_encode($options_a_retourner_ajax);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Données du voyage invalides pour les options.']);
    }
    exit; // Crucial pour ne pas envoyer le reste du HTML pour une requête AJAX
}
// --- FIN Gestion des requêtes AJAX ---


// --- Logique PHP pour l'affichage normal de la page ---
$nom_fichier_page = "json/{$id_page_voyage}.json";

if (!file_exists($nom_fichier_page)) {
    header("Location: circuits.php");
    exit;
}

$data_page = json_decode(file_get_contents($nom_fichier_page), true);
if (!$data_page) { // Vérification si le JSON est valide
    // Gérer l'erreur, par exemple rediriger ou afficher un message
    die("Erreur de chargement des données du voyage.");
}
$data_page['id'] = $id_page_voyage;

$_SESSION['recapitulatif_id'] = $id_page_voyage;

// Logique de traitement du formulaire POST (doit être adaptée pour utiliser $data_page comme source des prix)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
    // Copie des données de base du voyage chargé pour la page
    $recap_data = $data_page; // Important: $data_page contient les prix des options

    $recap_data['date_depart'] = $_POST['date_depart'] ?? null;
    // La durée est généralement fixe, issue de $data_page['duree'] ou $data_page['dates']['duree']
    // $recap_data['duree'] = $data_page['duree'] ?? ($data_page['dates']['duree'] ?? 10);

    $recap_data['nombre_personnes'] = $_POST['nombre_personnes'] ?? 0;
    $recap_data['personnes'] = $_POST['personnes'] ?? [];

    $prix_total_post = $recap_data['prix_base']; // Commencer avec le prix de base

    if (isset($_POST['options'])) {
        foreach ($_POST['options'] as $cle_etape_post => $options_choisies_pour_etape) {
            // $options_choisies_pour_etape est comme [0 => ['categorie1' => 'choixA', 'categorie2' => 'choixB']]
            // On accède aux options disponibles depuis $recap_data (qui est une copie de $data_page)
            if (isset($recap_data[$cle_etape_post][0]['options_disponibles'])) {
                $options_disponibles_json = $recap_data[$cle_etape_post][0]['options_disponibles'];
                
                // S'assurer que 'options_choisies' existe pour cette étape dans $recap_data
                if (!isset($recap_data[$cle_etape_post][0]['options_choisies'])) {
                    $recap_data[$cle_etape_post][0]['options_choisies'] = [];
                }

                foreach ($options_choisies_pour_etape[0] as $categorie_post => $choix_post) {
                    // Enregistrer le choix
                    $recap_data[$cle_etape_post][0]['options_choisies'][$categorie_post] = $choix_post;
                    // Ajouter le prix de l'option
                    if (isset($options_disponibles_json[$categorie_post][$choix_post])) {
                        $prix_total_post += (int) $options_disponibles_json[$categorie_post][$choix_post];
                    }
                }
            }
        }
    }
    $recap_data['prix_total'] = $prix_total_post; // Le prix total par personne

    $_SESSION['recapitulatif'] = $recap_data;
    header("Location: recapitulatif.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data_page['titre']) ?></title>
    <link rel="stylesheet" href="css/voyage.css">
    <link rel="stylesheet" href="css/panier.css">
    <link rel="icon" type="image/png" href="images/portail.png">
    <style>
        .options-placeholder .loading-spinner {
            border: 4px solid #f3f3f3; /* Light grey */
            border-top: 4px solid #c1f762; /* Votre couleur d'accent */
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin: 10px auto; /* Centrer le spinner */
        }
        .options-placeholder { /* Style pour le conteneur pendant le chargement */
            padding: 10px;
            border: 1px dashed #ccc;
            min-height: 50px;
            display: flex; /* Pour centrer le spinner */
            justify-content: center;
            align-items: center;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="dynamic-bg" style="background-image: url('<?= htmlspecialchars($data_page['image']) ?>');" data-voyage-id="<?= htmlspecialchars($id_page_voyage) ?>">

    <script src="js/theme.js"></script>

    <header class="header-top">
        <div class="logo-panier">
            <img src="images/portail.png" alt="Logo Tempus Odyssey" class="logo">
            <?php include 'panier.php'; ?>
        </div>
        <h1 class="site-title">
            <a href="index.php" style="text-decoration: none; color: inherit;">Tempus Odyssey</a>
        </h1>
        <button id="theme-toggle" class="btn">🌗</button>
        <nav aria-label="Navigation principale">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="circuits.php">Circuits</a></li>
                <?php if (!isset($_SESSION['user'])): ?>
                    <li><a href="inscription.php">Inscription</a></li>
                    <li><a href="connexion.php">Connexion</a></li>
                <?php else: ?>
                    <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="profil.php" class="active">Profil</a></li>
                    <li><a href="logout.php">Se déconnecter</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="voyage-form">
        <h2><?= htmlspecialchars($data_page['titre']) ?></h2>
        <p><?= htmlspecialchars($data_page['description']) ?></p>

        <form action="voyage.php?id=<?= urlencode($id_page_voyage) ?>" method="POST">
            <input type="hidden" name="id_voyage_form_hidden" value="<?= htmlspecialchars($id_page_voyage) ?>">

            <div class="voyageur">
                <?php if (!empty($data_page['dates']['choix_date_depart'])): ?>
                    <label>Date de départ</label>
                    <?php
                    $minDate = date('Y-m-d', strtotime('+1 day'));
                    $maxDate = date('Y-m-d', strtotime('+2 years'));
                    ?>
                    <input type="date" name="date_depart" required min="<?= $minDate ?>" max="<?= $maxDate ?>">
                <?php endif; ?>

                <label>Nombre de voyageurs</label>
                <input type="number" name="nombre_personnes" min="1" max="8" required placeholder="Entrez un nombre de voyageurs (max. 8)" value="1">

                <h4>Voyageur principal</h4>
                <label>Nom :</label>
                <input type="text" name="personnes[0][nom]"
                        value="<?= isset($_SESSION['user']['nom']) ? htmlspecialchars($_SESSION['user']['nom']) : '' ?>"
                        <?= isset($_SESSION['user']) ? 'readonly' : 'required' ?>
                        placeholder="Nom du voyageur">
                <label>Prénom :</label>
                <input type="text" name="personnes[0][prenom]"
                    value="<?= isset($_SESSION['user']['prenom']) ? htmlspecialchars($_SESSION['user']['prenom']) : '' ?>"
                    <?= isset($_SESSION['user']) ? 'readonly' : 'required' ?>
                    placeholder="Prénom du voyageur">
                <label>Date de naissance :</label>
                <input type="date" name="personnes[0][date_naissance]"
                    value="<?= isset($_SESSION['user']['date_naissance']) ? htmlspecialchars($_SESSION['user']['date_naissance']) : '' ?>"
                    <?= isset($_SESSION['user']) ? 'readonly' : 'required' ?>
                    placeholder="Date de naissance">
            </div>

            <h3>Étapes</h3>
            <div class="etapes-wrapper">
                <?php
                // Générer les placeholders pour chaque étape définie dans le JSON du voyage
                // JavaScript remplira ces placeholders avec les options.
                foreach ($data_page as $cle_etape_html => $valeur_etape_html) {
                    if (preg_match('/^etape\d+$/', $cle_etape_html)) {
                        echo "<div class='step' data-etape-cle='{$cle_etape_html}'>"; // Le JS utilisera data-etape-cle
                        echo "<h4>" . ucfirst($cle_etape_html) . "</h4>";
                        // Placeholder pour les options de cette étape
                        // L'ID n'est plus strictement nécessaire si on cible bien via data-etape-cle
                        echo "<div class='options-placeholder'>";
                        echo "<div class='loading-spinner'></div>"; // Afficher un spinner
                        echo "</div>";
                        echo "</div>";
                    }
                }
                ?>
            </div>

            <p class="prix-total">Prix total : <strong data-prix-base="<?= htmlspecialchars($data_page['prix_base']) ?>"><?= number_format($data_page['prix_base'], 0, ',', ' ') ?> €</strong></p>

            <?php if (isset($_SESSION['user'])): ?>
                <button type="submit" class="btn-valider">Valider la personnalisation</button>
            <?php else: ?>
                <p style="color: #c1f762; font-weight: bold;">
                    <a href="connexion.php" style="color: #c1f762;">Connectez-vous</a> avant de continuer.
                </p>
            <?php endif; ?>
        </form>

        <a href="circuits.php" class="retour-circuits">← Retour aux circuits</a>
    </main>

    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>
    <script src="js/voyage.js"></script>
    <script src="js/panier.js"></script>
</body>
</html>
