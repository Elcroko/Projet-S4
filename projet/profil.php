<?php
session_start();

$file = 'json/utilisateurs.json';

if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit;
}

$userEmail = $_SESSION['user']['email'];
$user = null;

if (file_exists($file)) {
    $data = file_get_contents($file);
    $users = json_decode($data, true);

    foreach ($users as $u) {
        if ($u['email'] === $userEmail) {
            $user = $u;
            break;
        }
    }
}

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Tempus Odyssey</title>
    <link rel="stylesheet" href="css/profil.css">
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
    <main class="profil-container">
        <div class="profil-header">
            <div class="profil-photo">
                <img src="images/profil.jpg" alt="Photo de profil">
                <button class="edit-photo-btn">Changer</button>
            </div>
            <h2><?= htmlspecialchars($user['prenom']) . ' ' . htmlspecialchars($user['nom']) ?></h2>
            <p class="profil-email"><?= htmlspecialchars($user['email']) ?></p>
        </div>
        
        <form class="profil-form">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" value="<?= htmlspecialchars($user['nom']) ?>" readonly>
                <button type="button" class="edit-btn"></button>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                <button type="button" class="edit-btn"></button>
            </div>
            <div class="form-group">
                <label for="date_naissance">Date de naissance :</label>
                <input type="text" id="date_naissance" value="<?= htmlspecialchars($user['date_naissance']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="date_inscription">Date d'inscription :</label>
                <input type="text" id="date_inscription" value="<?= htmlspecialchars($user['date_inscription']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="nombre_voyages">Nombre de voyages :</label>
                <input type="text" id="nombre_voyages" value="<?= htmlspecialchars($user['nombre_voyages']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone :</label>
                <input type="tel" id="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" readonly>
            </div>

            
            <button type="submit" class="save-btn">Enregistrer les modifications</button>
        </form>
    </main>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>

</body>
</html>
