<?php
session_start();

$id = $_GET['id'] ?? null;
$nom_fichier = "json/" . basename($id) . ".json";


if (!$id || !file_exists($nom_fichier)) {
    header("Location: circuits.php");
    exit;
}

$data = json_decode(file_get_contents($nom_fichier), true);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['titre']) ?> - Tempus Odyssey</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/voyage.css">
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
    <h2><?= htmlspecialchars($data['titre']) ?></h2>
    
    <section class="voyage-infos">
        <p><strong>Dates :</strong> du <?= $data['dates']['debut'] ?> au <?= $data['dates']['fin'] ?> (<?= $data['dates']['duree'] ?>)</p>
        <p><strong>Spécificités :</strong> <?= $data['specifications'] ?></p>
        <p><strong>Prix :</strong> <?= $data['prix'] ?> €</p>
        <p><strong>Statut :</strong> <?= $data['statut'] ?></p>
    </section>

    <section class="etapes">
        <h3>Étapes du voyage</h3>
        <?php foreach ($data['etapes'] as $etape): ?>
            <div class="etape">
                <h4><?= $etape['titre'] ?></h4>
                <p><strong>Dates :</strong> du <?= $etape['dates']['arrivee'] ?> au <?= $etape['dates']['depart'] ?> (<?= $etape['dates']['duree'] ?>)</p>
                <p><strong>Lieu :</strong> <?= $etape['position']['ville'] ?> (GPS : <?= $etape['position']['gps'] ?>)</p>
                <h5>Options</h5>
                <ul>
                    <?php foreach ($etape['options'] as $option => $valeur): ?>
                        <li><strong><?= ucfirst($option) ?> :</strong> <?= $valeur ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if (!empty($etape['personnes_supplementaires'])): ?>
                    <p><strong>Personnes supplémentaires :</strong> <?= implode(", ", $etape['personnes_supplementaires']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="participants">
        <h3>Participants</h3>
        <ul>
            <?php foreach ($data['personnes'] as $personne): ?>
                <li><?= $personne ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <?php if (isset($data['paiement'])): ?>
        <section class="paiement">
            <h3>Informations de paiement</h3>
            <p><strong>Carte :</strong> <?= $data['paiement']['carte'] ?></p>
            <p><strong>Expiration :</strong> <?= $data['paiement']['expiration'] ?></p>
            <p><strong>Date de transaction :</strong> <?= $data['paiement']['transaction_date'] ?></p>
        </section>
    <?php endif; ?>

    <?php if (isset($_SESSION['user'])): ?>
        <form action="recap.php" method="post">
            <input type="hidden" name="titre" value="<?= htmlspecialchars($data['titre']) ?>">
            <button type="submit" class="btn">Personnaliser ce voyage</button>
        </form>
    <?php endif; ?>

    <a href="circuits.php" class="btn">← Retour aux circuits</a>
</main>

<footer>
    <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
</footer>
</body>
</html>
