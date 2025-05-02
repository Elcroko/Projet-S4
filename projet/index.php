<?php
session_start();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempus Odyssey - Voyagez à travers le temps</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/png" href="images/portail.png">
</head>
<body>
    <!-- Détection du thème sombre -->
    <script src="js/theme.js"></script>

    <!-- En-tête -->
    <header>
        <img src="images/portail.png" alt="Logo Tempus Odyssey" class="logo">
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

    <!-- Contenu principal -->
    <main>
        <!-- Section d'introduction -->
        <section class="hero">
            <div class="hero-content">
                <h2 class="hero-title">Embarquez pour un voyage à travers le temps</h2>
                <p class="hero-subtitle">Revivez les époques légendaires et percez les mystères du futur.</p>
                <a href="circuits.php" class="btn">Explorer</a>
            </div>
        </section>

        <!-- Section de description -->
        <section class="about">
            <div class="about-container">
                <h3>Bienvenue sur Tempus Odyssey</h3>
                <p>
                    Découvrez un concept révolutionnaire de voyage temporel qui vous permet de parcourir les âges comme jamais auparavant. 
                    Que vous soyez passionné par l’histoire, curieux du futur ou en quête d’expériences inédites, nous avons conçu pour vous des circuits immersifs qui vous transporteront à travers les époques.
                </p>
                <p>
                    Au cœur de la Rome antique aux secrets du Moyen Âge, en passant par les grandes découvertes et les futurs possibles de l’humanité, 
                    nos voyages sont sécurisés, accompagnés de guides spécialisés et adaptés à tous les aventuriers du temps.
                </p>
                <p>
                    Osez l’aventure et devenez un explorateur temporel dès aujourd’hui !
                </p>
            </div>
        </section>


        <!-- Section des circuits temporels -->
        <section class="featured">
            <h3>Nos Voyages les Plus Demandés </h3>
            <div class="circuits-container">
                <?php
                    $topVoyages = [];

                    for ($i = 10; $i <= 15; $i++) {
                        $fichier = "json/voyage" . str_pad($i, 2, '0', STR_PAD_LEFT) . ".json";
                        if (file_exists($fichier)) {
                            $data = json_decode(file_get_contents($fichier), true);
                            if ($data) {
                                $data['id'] = "voyage" . str_pad($i, 2, '0', STR_PAD_LEFT); 
                                $topVoyages[] = $data;
                            }
                        }
                    }
                    
                    foreach ($topVoyages as $voyage): ?>
                        <a href="voyage.php?id=<?= htmlspecialchars($voyage['id']) ?>" class="circuit-link">
                            <div class="circuit">
                                <img src="<?= htmlspecialchars($voyage['image']) ?>" alt="Illustration du circuit <?= htmlspecialchars($voyage['titre']) ?>" />
                                <h4><?= htmlspecialchars($voyage['titre']) ?></h4>
                                <p><?= htmlspecialchars($voyage['description']) ?></p>

                                <?php if (isset($voyage['dates']['duree'])): ?>
                                    <p><strong>Durée :</strong> <?= htmlspecialchars($voyage['dates']['duree']) ?></p>
                                <?php endif; ?>

                                <?php if (isset($voyage['prix_base'])): ?>
                                    <p><strong>A partir de </strong> <?= htmlspecialchars($voyage['prix_base']) ?> €</p>
                                <?php endif; ?>

                                <?php
                                $nbEtapes = 0;
                                foreach ($voyage as $cle => $valeur) {
                                    if (str_starts_with($cle, 'etape')) $nbEtapes++;
                                }
                                ?>
                                <p><strong>Nombre d’étapes :</strong> <?= $nbEtapes ?></p>
                            </div>
                        </a>

                    <?php endforeach; ?>
            </div>
        </section>

    </main>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>
</body>
</html>
