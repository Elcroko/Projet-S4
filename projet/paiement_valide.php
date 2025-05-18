<?php
$page_title = "Paiement validé - Tempus Odyssey";
$css_file = "paiement.css";
require_once 'verif_banni.php';

if (isset($_SESSION['recapitulatif_id']) && isset($_SESSION['panier'])) {
    $id = $_SESSION['recapitulatif_id'];
    foreach ($_SESSION['panier'] as &$voyage) {
        if (isset($voyage['id']) && $voyage['id'] === $id) {
            $voyage['paiement'] = true;
            break;
        }
    }
    unset($voyage);
}

if (!isset($_SESSION['recapitulatif']) || !isset($_SESSION['user'], $_SESSION['cybank_transaction'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['paiement_enregistre']) || $_SESSION['paiement_enregistre'] !== $_SESSION['cybank_transaction']) {
    $commande = [
        'utilisateur' => $_SESSION['user'],
        'voyage' => $_SESSION['recapitulatif'],
        'date' => date('Y-m-d H:i:s'),
        'montant' => $_SESSION['cybank_montant'] ?? 0,
        'transaction' => $_SESSION['cybank_transaction'] ?? 'inconnue',
    ];

    if (!file_exists('paiements_json')) {
        mkdir('paiements_json');
    }

    $id_utilisateur = $_SESSION['user']['id'];
    $datetime = date("Y-m-d_H.i.s");
    $filename = "paiements_json/commande_user{$id_utilisateur}_{$datetime}.json";
    file_put_contents($filename, json_encode($commande, JSON_PRETTY_PRINT));

    $titre = $commande['voyage']['titre'] ?? "Voyage inconnu";
    $prix = $commande['montant'] ?? 0;
    $historique_entree = [
        'titre' => $titre,
        'prix' => $prix,
        'fichier_commande' => basename($filename)
    ];

    $utilisateursFile = 'json/utilisateurs.json';
    if (file_exists($utilisateursFile)) {
        $utilisateurs = json_decode(file_get_contents($utilisateursFile), true);
        foreach ($utilisateurs as &$utilisateur) {
            if ($utilisateur['email'] === $_SESSION['user']['email']) {
                if (!isset($utilisateur['historique'])) {
                    $utilisateur['historique'] = [];
                }

                $deja_present = false;
                foreach ($utilisateur['historique'] as $voyage) {
                    if ($voyage['fichier_commande'] === $historique_entree['fichier_commande']) {
                        $deja_present = true;
                        break;
                    }
                }

                if (!$deja_present) {
                    $utilisateur['historique'][] = $historique_entree;
                    $utilisateur['nombre_voyages'] = ($utilisateur['nombre_voyages'] ?? 0) + 1;
                    $_SESSION['user']['nombre_voyages'] = $utilisateur['nombre_voyages'];
                }

                break;
            }
        }
        file_put_contents($utilisateursFile, json_encode($utilisateurs, JSON_PRETTY_PRINT));
    }

    $_SESSION['paiement_enregistre'] = $_SESSION['cybank_transaction'];
    unset($_SESSION['recapitulatif'], $_SESSION['cybank_montant'], $_SESSION['cybank_transaction']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php include 'includes/head.php'; ?>

<body>
<?php include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <h1>Paiement validé</h1>
            <p>Votre paiement a été confirmé avec succès. Merci pour votre réservation !</p>
            <div class="retour-voyage">
                <a href="index.php" class="retour-btn">Retour à l'accueil</a>
                <a href="profil.php" class="retour-btn">Voir mon profil</a>
            </div>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
