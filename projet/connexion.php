<?php
session_start();

// Si l'utilisateur est déjà connecté, le rediriger vers la page d'accueil ou son profil
if (isset($_SESSION['user'])) {
    header("Location: index.php"); // Ou profil.php si vous préférez
    exit;
}

$fichier_utilisateurs = "json/utilisateurs.json";
$erreurs = []; // Tableau pour stocker les messages d'erreur
$email_retenu = ''; // Pour conserver l'email en cas d'erreur de mot de passe

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
    $mot_de_passe = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email)) {
        $erreurs['email'] = "L'adresse e-mail est requise.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "Le format de l'adresse e-mail est invalide.";
    }

    if (empty($mot_de_passe)) {
        $erreurs['password'] = "Le mot de passe est requis.";
    }

    if (empty($erreurs)) { // Si les champs sont remplis et l'email a un format valide
        if (file_exists($fichier_utilisateurs)) {
            $utilisateurs_data = file_get_contents($fichier_utilisateurs);
            $utilisateurs = json_decode($utilisateurs_data, true);

            $utilisateur_trouve = null;
            $email_existe = false;

            foreach ($utilisateurs as $user) {
                if ($user['email'] === $email) {
                    $email_existe = true;
                    if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                        if (!empty($user['banni'])) {
                            $erreurs['general'] = "❌ Votre compte a été banni. Accès refusé.";
                            // Pas de redirection vers banni.php ici pour garder le message sur la page de connexion
                            break;
                        }
                        $utilisateur_trouve = $user;
                        break;
                    } else {
                        // L'email est correct, mais le mot de passe est incorrect
                        $erreurs['password'] = "Mot de passe incorrect.";
                        $email_retenu = $email; // On retient l'email pour le réafficher
                        break;
                    }
                }
            }

            if (!$email_existe && empty($erreurs['general'])) {
                $erreurs['email'] = "Aucun compte trouvé avec cette adresse e-mail.";
            }

            if ($utilisateur_trouve && empty($erreurs['general'])) {
                // Connexion réussie
                $_SESSION['user'] = [
                    'id' => $utilisateur_trouve['id'],
                    'nom' => $utilisateur_trouve['nom'],
                    'prenom' => $utilisateur_trouve['prenom'],
                    'email' => $utilisateur_trouve['email'],
                    'date_naissance' => $utilisateur_trouve['date_naissance']
                    // Ajoutez d'autres informations utiles ici si nécessaire
                ];
                $_SESSION['role'] = !empty($utilisateur_trouve['admin']) ? 'admin' : 'user';
                header("Location: profil.php"); // Redirection vers le profil après connexion
                exit;
            }
        } else {
            $erreurs['general'] = "Erreur système. Veuillez réessayer plus tard.";
        }
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
    <style>
        .error-message { /* Style de base pour les messages d'erreur PHP */
            color: #FF0000; /* Rouge pour les erreurs */
            font-size: 0.9em;
            margin-top: 5px;
        }
        .form-container .error-message { /* Pour cibler spécifiquement les erreurs sous les champs */
            text-align: left;
            margin-left: 5px; /* Petite marge pour l'alignement */
        }
        .erreur-connexion { /* Style pour l'erreur générale */
            background-color: #ffdddd;
            border-left: 6px solid #f44336;
            color: #5f2120;
            padding: 15px;
            margin-top: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <script src="js/theme.js"></script>

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

                <?php if (isset($erreurs['general'])): ?>
                    <div class="erreur-connexion"><?= htmlspecialchars($erreurs['general']) ?></div>
                <?php endif; ?>

                <form id="form-connexion" method="POST" action="connexion.php" novalidate>
                    <div>
                        <input type="email" id="email" name="email" placeholder="Adresse email"
                               value="<?= htmlspecialchars($email_retenu) ?>" required>
                        <?php if (isset($erreurs['email'])): ?>
                            <div class="error-message" id="email-php-error"><?= htmlspecialchars($erreurs['email']) ?></div>
                        <?php endif; ?>
                        <div class="error-message" id="email-js-error"></div> </div>

                    <div style="position:relative;">
                        <input type="password" id="password" name="password" placeholder="Mot de passe" required autocomplete="new-password">
                        <span id="toggle-password" class="eye-icon">👁️</span>
                        <?php if (isset($erreurs['password'])): ?>
                            <div class="error-message" id="password-php-error"><?= htmlspecialchars($erreurs['password']) ?></div>
                        <?php endif; ?>
                        <div class="error-message" id="password-js-error"></div> </div>

                    <button type="submit">Se connecter</button>
                    <a class="forgot-password" href="inscription.php">Créer un compte</a>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>

    <script src="js/verif_connexion.js"></script>
</body>
</html>
