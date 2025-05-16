<!-- header.php -->
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

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
