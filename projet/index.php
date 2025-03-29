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
                <a href="voyage.php?id=voyage01" class="circuit-link">
                    <article class="circuit">
                        <img src="images/morts.jpeg" alt="Illustration du circuit Le jour de votre Mort">
                        <h4>Le Jour de votre Mort</h4>
                        <p>Oserez-vous affronter votre destinée et découvrir ce que l’avenir vous réserve ?</p>
                    </article>
                </a>

                <a href="voyage.php?id=voyage02" class="circuit-link">
                <article class="circuit">
                    <img src="images/udino.jpeg" alt="Illustration du circuit La Préhistoire">
                    <h4>La Préhistoire</h4>
                    <p>Évitez les prédateurs préhistoriques et survivez dans un monde sauvage et impitoyable.</p>
                </article>
            </a>

            <a href="voyage.php?id=voyage03" class="circuit-link">
                <article class="circuit">
                    <img src="images/Fin_du_monde.jpeg" alt="Illustration du circuit Fin du Monde">
                    <h4>Fin du Monde</h4>
                    <p>Vivez en direct l’apocalypse et assistez aux derniers instants de l’humanité.</p>
                </article>
            </a>

            <a href="voyage.php?id=voyage04" class="circuit-link">
                <article class="circuit">
                    <img src="images/vinkings.png" alt="Vikings">
                    <h4>L'Époque des Vikings</h4>
                    <p>Rejoignez Ragnar et ses guerriers pour des raids épiques et une conquête sans pitié.</p>
                </article>
            </a>

            <a href="voyage.php?id=voyage06" class="circuit-link">
                <article class="circuit">
                    <img src="images/coin.jpeg" alt="bitcoin">
                    <h4>L'Ère du Bitcoin</h4>
                    <p>Voyagez dans le passé et changez votre destinée financière en maîtrisant la cryptomonnaie.</p>
                </article>
            </a>

            <a href="voyage.php?id=voyage07" class="circuit-link">
                <article class="circuit">
                    <img src="images/Colomb.jpeg" alt="colomb">
                    <h4>À Bord avec Christophe Colomb</h4>
                    <p>Traversez l’Atlantique et assistez à la découverte d’un Nouveau Monde.</p>
                </article>
            </a>

            </div>
        </section>

    </main>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>
</body>
</html>

