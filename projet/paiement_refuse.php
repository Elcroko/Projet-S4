<?php session_start(); 


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement refusé</title>
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

    <main>
        <div class="container">
            <h1> Paiement refusé</h1>
            <p>Votre paiement a échoué. Cela peut être dû à un numéro incorrect ou à un manque de fonds.</p>
            <div class="retour-voyage">
                <a href="cybank.php" class="retour-btn"> Réessayer le paiement</a>
                <a href="recapitulatif.php" class="retour-btn"> Modifier le voyage</a>
            </div>
        </div>
    </main>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>

</body>
</html>
