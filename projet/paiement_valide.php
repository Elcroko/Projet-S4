<?php
session_start();

if (isset($_SESSION['recapitulatif_id']) && isset($_SESSION['panier'])) {
    $id = $_SESSION['recapitulatif_id'];

    // Parcourir le panier et mettre à jour l'état de paiement
    foreach ($_SESSION['panier'] as &$voyage) {
        if (isset($voyage['id']) && $voyage['id'] === $id) {
            $voyage['paiement'] = true;
            break;
        }
    }
    unset($voyage); // Bonnes pratiques pour éviter des références persistantes
}


if (!isset($_SESSION['recapitulatif']) || !isset($_SESSION['user'],$_SESSION['cybank_transaction'])) {
    header('Location: index.php');
    exit;
}

// Si la transaction a déjà été enregistrée, ne pas la retraiter
if (isset($_SESSION['paiement_enregistre']) && $_SESSION['paiement_enregistre'] === $_SESSION['cybank_transaction']) {
    // Affichage seulement (aucune modification)
} else {

    $commande = [
        'utilisateur' => $_SESSION['user'],
        'voyage' => $_SESSION['recapitulatif'],
        'date' => date('Y-m-d H:i:s'),
        'montant' => $_SESSION['cybank_montant'] ?? 0,
        'transaction' => $_SESSION['cybank_transaction'] ?? 'inconnue',
    ];

    // Sauvegarde dans un fichier JSON
    if (!file_exists('paiements_json')) {
        mkdir('paiements_json');
    }
    $id_utilisateur = $_SESSION['user']['id'];
    $datetime = date("Y-m-d_H.i.s"); // Ex: 2025-04-06_14-30-12
    $filename = "paiements_json/commande_user{$id_utilisateur}_{$datetime}.json";

    file_put_contents($filename, json_encode($commande, JSON_PRETTY_PRINT));

    // Préparation d’une entrée pour l’historique
    $titre = $commande['voyage']['titre'] ?? "Voyage inconnu";
    $prix = $commande['montant'] ?? 0;
    $historique_entree = [
        'titre' => $titre,
        'prix' => $prix,
        'fichier_commande' => basename($filename)
    ];

    // Incrémenter le nombre de voyages de l'utilisateur
    $utilisateursFile = 'json/utilisateurs.json';
    if (file_exists($utilisateursFile)) {
        $utilisateurs = json_decode(file_get_contents($utilisateursFile), true);
        foreach ($utilisateurs as &$utilisateur) {
            if ($utilisateur['email'] === $_SESSION['user']['email']) {
                if (!isset($utilisateur['historique'])) {
                    $utilisateur['historique'] = [];
                }
                // 🔒 Empêcher les doublons dans l’historique
                $deja_present = false;
                foreach ($utilisateur['historique'] as $voyage) {
                    if ($voyage['fichier_commande'] === $historique_entree['fichier_commande']) {
                        $deja_present = true;
                        break;
                    }
                }
                if (!$deja_present) {
                    $utilisateur['historique'][] = $historique_entree;

                    // ✅ Incrémenter le compteur une seule fois ici
                    if (!isset($utilisateur['nombre_voyages'])) {
                        $utilisateur['nombre_voyages'] = 1;
                    } else {
                        $utilisateur['nombre_voyages']++;
                    }

                    // Mise à jour session
                    $_SESSION['user']['nombre_voyages'] = $utilisateur['nombre_voyages'];
                }

                break;
            }
        }
        file_put_contents($utilisateursFile, json_encode($utilisateurs, JSON_PRETTY_PRINT));
    }

    // ✅ Marquer comme enregistré (pour éviter les doubles traitements)
    $_SESSION['paiement_enregistre'] = true;

    // 🧹 Supprime les infos sensibles (nettoyage après paiement)
    unset($_SESSION['recapitulatif']);
    unset($_SESSION['cybank_montant']);
    unset($_SESSION['cybank_transaction']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement validé</title>
    <link rel="stylesheet" href="css/paiement.css">
    <link rel="stylesheet" href="css/panier.css">
    <link rel="icon" type="image/png" href="images/portail.png">

</head>
<body>
    <!-- Détection du thème sombre -->
    <script src="js/theme.js"></script>

    <!-- En-tête -->
    <header class="header-top">
        <div class="logo-panier">
            <img src="images/portail.png" alt="Logo Tempus Odyssey" class="logo">
            <?php include 'panier.php'; ?>
        </div>
        
        <h1 class="site-title">
            <a href="index.php" style="text-decoration: none; color: inherit;">Tempus Odyssey</a>
        </h1> 
        
        <button id="theme-toggle" class="btn">Changer de thème</button>

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

    <main>
        <div class="container">
            <h1> Paiement validé</h1>
            <p>Votre paiement a été confirmé avec succès. Merci pour votre réservation !</p>

            <div class="retour-voyage">
                <a href="index.php" class="retour-btn">Retour à l'accueil</a>
                <a href="profil.php" class="retour-btn">Voir mon profil</a>
            </div>
        </div>
    </main>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>
    
</body>
</html>

