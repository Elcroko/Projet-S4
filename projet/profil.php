<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit;
}

// Récupérer les données de l'utilisateur depuis la session
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Tempus Odyssey</title>
    <link rel="stylesheet" href="css/styles.css">
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
                <li><a href="inscription.php">Inscription</a></li>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="profil.php" class="active">Profil</a></li>
                <li><a href="logout.php" class="logout-btn">Se déconnecter</a></li>
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
                <input type="text" id="date_naissance" value="<?= isset($user['date_de_naissance']) ? htmlspecialchars($user['date_de_naissance']) : 'Non renseigné' ?>" readonly>
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone :</label>
                <input type="tel" id="telephone" value="000000"><!-- ?= htmlspecialchars($user['telephone']) ?>" readonly--> 
                <button type="button" class="edit-btn"></button>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse :</label>
                <input type="text" id="adresse" value="000000"> <!-- ?= htmlspecialchars($user['adresse']) ? readonly-->
                
                <button type="button" class="edit-btn"></button>
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
