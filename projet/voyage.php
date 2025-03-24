<?php
session_start();

// Liste des circuits avec leurs infos associées
$circuits = [
    "Le Jour de votre Mort" => [
        "image" => "images/mort.jpeg",
        "description" => "Oserez-vous affronter votre destinée et découvrir ce que l’avenir vous réserve ?"
    ],
    "La Préhistoire" => [
        "image" => "images/Udino.jpeg",
        "description" => "Évitez les prédateurs préhistoriques et survivez dans un monde sauvage et impitoyable."
    ],
    "Fin du Monde" => [
        "image" => "images/fin_du_monde.jpeg",
        "description" => "Vivez en direct l’apocalypse et assistez aux derniers instants de l’humanité."
    ],
    "L'Époque des Vikings" => [
        "image" => "images/vinkings.jpeg",
        "description" => "Rejoignez Ragnar et ses guerriers pour des raids épiques et une conquête sans pitié."
    ],
    "À la Cour du Roi Soleil" => [
        "image" => "images/chateau.jpeg",
        "description" => "Vivez dans le faste du château de Versailles et assistez aux intrigues royales."
    ],
    "L'Ère du Bitcoin" => [
        "image" => "images/bitcoin.jpeg",
        "description" => "Voyagez dans le passé et changez votre destinée financière en maîtrisant la cryptomonnaie."
    ],
    "À Bord avec Christophe Colomb" => [
        "image" => "images/colomb.jpeg",
        "description" => "Traversez l’Atlantique et assistez à la découverte d’un Nouveau Monde."
    ],
    "Le Secret des Pyramides" => [
        "image" => "images/pyramides.jpeg",
        "description" => "Assistez à la construction des pyramides et découvrez leurs mystères."
    ],
    "Révolution à Paris" => [
        "image" => "images/bastille.jpeg",
        "description" => "Vivez la prise de la Bastille et plongez en pleine Révolution française."
    ],
    "L'Enfer des Tranchées" => [
        "image" => "images/1ere_gm.jpeg",
        "description" => "Expérimentez la dure réalité des soldats de la Première Guerre mondiale."
    ],
    "Mission Résistance" => [
        "image" => "images/2eme_gm.jpeg",
        "description" => "Rejoignez la Résistance et luttez contre l’occupation nazie."
    ],
    "Croisière Interplanétaire" => [
        "image" => "images/croisiere_interplanetaire.jpeg",
        "description" => "Embarquez pour un voyage à travers les étoiles et explorez les confins de l’univers."
    ],
    "Jeux Olympiques de l'An 3000" => [
        "image" => "images/jo_3000.jpeg",
        "description" => "Assistez aux performances incroyables des athlètes du futur dans un stade ultra-technologique."
    ]
];

// Récupérer le titre depuis l’URL
$titre = isset($_GET['titre']) ? urldecode($_GET['titre']) : null;

// Si le voyage existe dans le tableau
if ($titre && isset($circuits[$titre])) {
    $info = $circuits[$titre];
} else {
    header("Location: circuits.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titre) ?> - Tempus Odyssey</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
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
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="logout.php">Se déconnecter</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main class="voyage-container">
    <h2><?= htmlspecialchars($titre) ?></h2>
    <img src="<?= htmlspecialchars($info['image']) ?>" alt="<?= htmlspecialchars($titre) ?>" class="voyage-img">
    <p class="voyage-description"><?= htmlspecialchars($info['description']) ?></p>
    <a href="circuits.php" class="btn">← Retour aux circuits</a>
</main>

<footer>
    <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
</footer>
</body>
</html>
