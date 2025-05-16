<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$fichier = "json/utilisateurs.json";
$erreur_connexion = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST['email']));
    $mot_de_passe = $_POST['password'];

    $utilisateurs = json_decode(file_get_contents($fichier), true);

    $utilisateur_trouve = null;

    foreach ($utilisateurs as $user) {
        if ($user['email'] === $email && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            if (!empty($user['banni'])) {
                // ✅ L'utilisateur est banni → on bloque
                $erreur_connexion = "❌ Vous avez été banni. Accès refusé.";
                break;
            }

            // ✅ L'utilisateur est valide
            $utilisateur_trouve = $user;
            break;
        }
    }

    if ($utilisateur_trouve) {
        $_SESSION['user'] = [
            'id' => $utilisateur_trouve['id'],
            'nom' => $utilisateur_trouve['nom'],
            'prenom' => $utilisateur_trouve['prenom'],
            'email' => $utilisateur_trouve['email'],
            'date_naissance' => $utilisateur_trouve['date_naissance']
        ];
        $_SESSION['role'] = !empty($utilisateur_trouve['admin']) ? 'admin' : 'user';
        header("Location: profil.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Tempus Odyssey</title>
    <link rel="icon" type="image/png" href="images/portail.png">
    <link rel="stylesheet" href="css/connexion.css">
</head>
<body>
    <!-- Verification du formulaire de connexion -->
    <script src="js/verif_connexion.js"></script> 
    
    <!-- Script pour le thème sombre -->
    <script src="js/theme.js"></script>

    <!-- En-tête -->
    <header>
        <img src="images/portail.png" alt="Logo Tempus Odyssey" class="logo">
        
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
    <section class="main-content">
        <div class="form-container">

            <h2>Connexion</h2>
            <p>Connectez-vous pour accéder à vos voyages temporels !</p>
                <form id="form-connexion" method="POST" action="connexion.php">

                    <input type="email" id="email" name="email" placeholder="Adresse email" required>

                    <div style="position:relative;">
                    <input type="password" id="password" name="password" placeholder="Mot de passe" required autocomplete="new-password">
                        <span id="toggle-password" class="eye-icon">👁️</span>
                    </div>

                    <button type="submit">Se connecter</button>

                    <a class="forgot-password" href="inscription.php">Créer un compte</a>
                </form>
                <?php if (!empty($erreur_connexion)): ?>
                    <div class="erreur-connexion">
                        <?= htmlspecialchars($erreur_connexion) ?>
                    </div>
                <?php endif; ?>
        </div>
    </section>

    </main>
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>
</body>
</html>
