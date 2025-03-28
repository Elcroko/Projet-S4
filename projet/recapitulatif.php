<?php
session_start();

if (!isset($_SESSION['recapitulatif'])) {
    header('Location: index.php');
    exit;
}

$data = $_SESSION['recapitulatif'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Récapitulatif du Voyage</title>
    <link rel="stylesheet" href="css/recapitulatif.css">
</head>
<body>
    <!-- En-tête -->
    <header>
        <img src="images/portail.png" alt="Logo Tempus Odyssey" class="logo">
        <h1 class="site-title">
            <a href="index.php" style="text-decoration: none; color: inherit;">Tempus Odyssey</a>
        </h1>        
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
                <h3>Voyageurs (<?= count($data['personnes']) ?>)</h3>
                <ul>
                    <?php foreach ($data['personnes'] as $voyageur): ?>
                        <li><?= htmlspecialchars($voyageur['prenom'] . ' ' . $voyageur['nom']) ?> (<?= (new DateTime($voyageur['date_naissance']))->diff(new DateTime())->y ?> ans)</li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="recap-block">
                <h3>Étapes et options choisies</h3>
                <?php foreach ($data as $cle => $etapes): ?>
                    <?php if (strpos($cle, 'etape') === 0 && is_array($etapes)): ?>
                        <div class="etape">
                            <h4><?= ucfirst($cle) ?></h4>
                            <ul>
                                <?php foreach ($etapes as $etape): ?>
                                    <li><strong>Position :</strong> <?= htmlspecialchars($etape['options_choisies']['position'] ?? 'Non sélectionné') ?></li>
                                    <li><strong>Activité :</strong> <?= htmlspecialchars($etape['options_choisies']['activite'] ?? 'Non sélectionné') ?></li>
                                    <li><strong>Hébergement :</strong> <?= htmlspecialchars($etape['options_choisies']['hebergement'] ?? 'Non sélectionné') ?></li>
                                    <li><strong>Restauration :</strong> <?= htmlspecialchars($etape['options_choisies']['restauration'] ?? 'Non sélectionné') ?></li>
                                    <li><strong>Transport :</strong> <?= htmlspecialchars($etape['options_choisies']['transport'] ?? 'Non sélectionné') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="price-total">Prix total : <?= number_format($data['prix_total'], 0, ',', ' ') ?> €</div>

            <div class="retour-voyage">
                <form method="get" action="voyage.php">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($_SESSION['recapitulatif']['id'] ?? 'voyage01') ?>">
                    <button type="submit" class="retour-btn">Modifier ce voyage</button>
                </form>
                <form action="cybank.php" method="post">
                    <button type="submit" class="retour-btn">Confirmer la personnalisation et passer au paiement</button>
                </form>
            </div>
        </div>

    </main>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>

</body>
</html>