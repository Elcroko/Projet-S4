<?php
session_start();
$isHistorique = false;
$commande = null;
$recapitulatif_id = 'voyage01'; // par défaut

// Si l'utilisateur consulte un ancien voyage via l'historique
if (isset($_GET['fichier'])) {
    $fichierHistorique = 'paiements_json/' . basename($_GET['fichier']);
    if (file_exists($fichierHistorique)) {
        $commande = json_decode(file_get_contents($fichierHistorique), true);
        $recapitulatif_id = $commande['voyage']['id'] ?? 'voyage01';
        $isHistorique = true;
        $data = $commande['voyage']; // ✅ on affiche les données de l’historique
    } else {
        echo "Erreur : fichier introuvable.";
        exit;
    }
}

// Sinon on affiche les données de la session (réservation en cours)
if (!$isHistorique) {
    if (!isset($_SESSION['recapitulatif'])) {
        header('Location: index.php');
        exit;
    }

    $data = $_SESSION['recapitulatif'];
    $recapitulatif_id = $_SESSION['recapitulatif_id'] ?? 'voyage01';

    // ✅ AJOUTER AU PANIER UNIQUEMENT SI NON HISTORIQUE
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // Empêche d’ajouter plusieurs fois la même personnalisation
    $idUnique = $recapitulatif_id . '_' . md5(serialize($data)); // hash basé sur les données

    $dejaDansPanier = false;
    foreach ($_SESSION['panier'] as $item) {
        if (isset($item['_uid']) && $item['_uid'] === $idUnique) {
            $dejaDansPanier = true;
            break;
        }
    }

    if (!$dejaDansPanier) {
        $data['_uid'] = $idUnique; // on garde une clé unique pour comparaison future
        $_SESSION['panier'][] = $data;
    }
}


function afficherOption($categorie, $valeur, $prix = 0) {
    if ($valeur && $valeur !== 'Aucune option') {
        return "<li><strong>" . ucfirst($categorie) . "</strong> : $valeur (+$prix €)</li>";
    } else {
        return "<li><strong>" . ucfirst($categorie) . "</strong> : Aucune option</li>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Récapitulatif du Voyage</title>
    <link rel="stylesheet" href="css/recapitulatif.css">
    <link rel="stylesheet" href="css/panier.css">
</head>
<body class="dynamic-bg" style="background-image: url('<?= htmlspecialchars($data['image']) ?>');">

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
    
    <!-- Contenu principal -->
    <main>
        <div class="recap-container">
            <h2><?= htmlspecialchars($data['titre']) ?></h2>
            <p><strong>Date de départ :</strong> <?= htmlspecialchars($data['date_depart']) ?></p>
            <p><strong>Durée :</strong> <?= htmlspecialchars($data['duree']) ?> jours</p>

            <div class="recap-block">
            <h3>Voyageurs (<?= htmlspecialchars($data['nombre_personnes'] ?? count($data['personnes'])) ?>)</h3>
                <ul>
                    <?php foreach ($data['personnes'] as $voyageur): ?>
                        <li><?= htmlspecialchars($voyageur['prenom'] . ' ' . $voyageur['nom']) ?> (<?= (new DateTime($voyageur['date_naissance']))->diff(new DateTime())->y ?> ans)</li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="recap-block">
                <h3>Étapes et options choisies</h3>
                <?php 
                $prix_total = $data['prix_base'] ?? 0;
                foreach ($data as $cle => $etapes): 
                    if (strpos($cle, 'etape') === 0 && is_array($etapes)): ?>
                        <div class="etape">
                            <h4><?= ucfirst($cle) ?></h4>
                            <ul>
                            <?php foreach ($etapes as $etape): ?>
                                <?php
                                if (!isset($etape['options_choisies']) || !is_array($etape['options_choisies'])) continue;
                                ?>
                                <ul>
                                <?php foreach ($etape['options_choisies'] ?? [] as $categorie => $option): ?>
                                        <?php
                                            $prix_option = 0;
                                            $available = $etape['options_disponibles'][$categorie] ?? [];
                                            if (is_array($available) && isset($available[$option])) {
                                                $prix_option = $available[$option];
                                            }
                                            
                                            $prix_total += $prix_option;
                                        ?>
                                        <li><strong><?= ucfirst($categorie) ?> :</strong> <?= htmlspecialchars($option) ?> (<?= $prix_option ?> €)</li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endforeach; ?>

                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php
                $nb_voyageurs = $data['nombre_personnes'] ?? 1;
                $prix_final = $prix_total * $nb_voyageurs;
                $_SESSION['cybank_montant'] = $prix_final;
                ?>
            </div>
            
            <div class="price-total">
                <div><strong>Sous-total par voyageur :</strong> <?= number_format($prix_total, 0, ',', ' ') ?> €</div>
                <div><strong>Nombre de voyageurs :</strong> <?= $nb_voyageurs ?></div>
                <div class="price-total"><strong>Prix total :</strong> <?= number_format($prix_final, 0, ',', ' ') ?> €</div>
            </div>

            
            <?php if (!$isHistorique): ?>
                <div class="retour-voyage">
                    <form action="voyage.php" method="get">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($recapitulatif_id ?? ($_SESSION['recapitulatif_id'] ?? 'voyage01')) ?>">
                        <button class="retour-btn">Modifier ce voyage</button>
                    </form>
                
                    <form action="cybank.php" method="post">
                        <button type="submit" class="retour-btn">Confirmer la personnalisation et passer au paiement</button>
                    </form>
                </div>
            <?php endif; ?>

        </div>
        <?php if ($isHistorique): ?>
            <div class="retour-voyage">
                <a href="profil.php" class="retour-btn">Retour au profil</a>
            </div>
        <?php endif; ?> 
    </main>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>

</body>
</html>
