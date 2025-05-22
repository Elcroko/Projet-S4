<?php
$page_title = "Récapitulatif - Tempus Odyssey";
$css_file = "recapitulatif.css";
require_once 'verif_banni.php';

$isHistorique = false;
$commande = null;
$recapitulatif_id = 'voyage01'; // par défaut

// Vérifie si un nom de fichier de commande est passé en paramètre
if (isset($_GET['fichier'])) {
    $fichierHistorique = 'paiements_json/' . basename($_GET['fichier']);
    if (file_exists($fichierHistorique)) {
        // Lit le contenu du fichier de commande JSON
        $commande = json_decode(file_get_contents($fichierHistorique), true);
        $recapitulatif_id = $commande['voyage']['id'] ?? 'voyage01';
        $isHistorique = true;
        $data = $commande['voyage'];
    } else {
        echo "Erreur : fichier introuvable.";
        exit;
    }
}

// Réservation en cours
if (!$isHistorique) {
    if (!isset($_SESSION['recapitulatif'])) {
        header('Location: index.php');
        exit;
    }

    $data = $_SESSION['recapitulatif'];
    $recapitulatif_id = $_SESSION['recapitulatif_id'] ?? 'voyage01';

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    $idUnique = $recapitulatif_id . '_' . md5(serialize($data));
    $dejaDansPanier = false;

    foreach ($_SESSION['panier'] as $item) {
        if (isset($item['_uid']) && $item['_uid'] === $idUnique) {
            $dejaDansPanier = true;
            break;
        }
    }

    if (!$dejaDansPanier) {
        $data['_uid'] = $idUnique;
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
<?php include 'includes/head.php'; ?>

<body>
<?php include 'includes/header.php'; ?>

<main>
    <div class="recap-container" data-uid="<?= htmlspecialchars($data['_uid'] ?? '') ?>">
        <h2><?= htmlspecialchars($data['titre']) ?></h2>
        <p><strong>Date de départ :</strong> <?= htmlspecialchars($data['date_depart'] ?? 'Non précisée') ?></p>
        <p><strong>Durée :</strong> <?= htmlspecialchars($data['duree'] ?? 'Inconnue') ?> jours</p>

        <div class="recap-block">
            <h3>Voyageurs (<?= htmlspecialchars($data['nombre_personnes'] ?? count($data['personnes'] ?? [])) ?>)</h3>
            <ul>
                <?php foreach ($data['personnes'] ?? [] as $voyageur): ?>
                    <li><?= htmlspecialchars($voyageur['prenom'] . ' ' . $voyageur['nom']) ?>
                        (<?= (new DateTime($voyageur['date_naissance']))->diff(new DateTime())->y ?> ans)
                    </li>
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
                                if (!isset($etape['options_choisies'])) continue;

                                // Prix de base de l'étape (s'il y en a)
                                $prix_etape = $etape['prix'] ?? 0;

                                // Boucle sur les options choisies
                                foreach ($etape['options_choisies'] as $categorie => $option): 
                                    $prix_option = $etape['options_disponibles'][$categorie][$option] ?? 0;
                                    $prix_etape += (float) $prix_option;
                                ?>
                                    <ul>
                                        <li>
                                            <strong><?= ucfirst($categorie) ?> :</strong>
                                            <?= htmlspecialchars($option) ?>
                                            <?= $prix_option > 0 ? "({$prix_option} €)" : '' ?>
                                        </li>
                                    </ul>
                                <?php endforeach; ?>

                                <?php $prix_total += $prix_etape; ?>
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
                    <input type="hidden" name="id" value="<?= htmlspecialchars($recapitulatif_id) ?>">
                    <input type="hidden" name="supprimer_uid" value="<?= htmlspecialchars($data['_uid'] ?? '') ?>">
                    <input type="hidden" name="retour_modification" value="1">
                    <button type="submit" class="retour-btn">Modifier ce voyage</button>
                </form>
                <form action="cybank.php" method="post">
                    <button type="submit" class="retour-btn">Confirmer la personnalisation et passer au paiement</button>
                </form>
            </div>
        <?php else: ?>
            <div class="retour-voyage">
                <a href="profil.php" class="retour-btn">Retour au profil</a>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
<script src="js/panier.js"></script>
</body>
</html>
