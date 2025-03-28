<?php
session_start();

if (!isset($_SESSION['recapitulatif']) || !isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

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

// Enregistre le fichier sans doublon
file_put_contents($filename, json_encode($commande, JSON_PRETTY_PRINT));

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement validé</title>
    <link rel="stylesheet" href="css/paiement.css">
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

