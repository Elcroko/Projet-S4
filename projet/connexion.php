<?php
$page_title = "Connexion - Tempus Odyssey";
$css_file = "connexion.css";
require_once 'verif_banni.php';

// Rediriger si l'utilisateur est déjà connecté
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$fichier_utilisateurs = "json/utilisateurs.json";
$erreurs = [];
$email_retenu = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
    $mot_de_passe = $_POST['password'] ?? '';

    if (empty($email)) {
        $erreurs['email'] = "L'adresse e-mail est requise.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "Le format de l'adresse e-mail est invalide.";
    }

    if (empty($mot_de_passe)) {
        $erreurs['password'] = "Le mot de passe est requis.";
    }

    if (empty($erreurs)) {
        if (file_exists($fichier_utilisateurs)) {
            $utilisateurs = json_decode(file_get_contents($fichier_utilisateurs), true);
            $utilisateur_trouve = null;
            $email_existe = false;

            foreach ($utilisateurs as $user) {
                if ($user['email'] === $email) {
                    $email_existe = true;

                    if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
                        if (!empty($user['banni'])) {
                            $erreurs['general'] = "❌ Votre compte a été banni. Accès refusé.";
                            break;
                        }

                        $utilisateur_trouve = $user;
                        break;
                    } else {
                        $erreurs['password'] = "Mot de passe incorrect.";
                        $email_retenu = $email;
                        break;
                    }
                }
            }

            if (!$email_existe && empty($erreurs['general'])) {
                $erreurs['email'] = "Aucun compte trouvé avec cette adresse e-mail.";
            }

            if ($utilisateur_trouve && empty($erreurs['general'])) {
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
        } else {
            $erreurs['general'] = "Erreur système. Veuillez réessayer plus tard.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/header.php'; ?>

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
                        <div class="error-message" id="email-js-error"></div>
                    </div>

                    <div style="position:relative;">
                        <input type="password" id="password" name="password" placeholder="Mot de passe" required autocomplete="new-password">
                        <span id="toggle-password" class="eye-icon">👁️</span>
                        <?php if (isset($erreurs['password'])): ?>
                            <div class="error-message" id="password-php-error"><?= htmlspecialchars($erreurs['password']) ?></div>
                        <?php endif; ?>
                        <div class="error-message" id="password-js-error"></div>
                    </div>

                    <button type="submit">Se connecter</button>
                    <a class="forgot-password" href="inscription.php">Créer un compte</a>
                </form>
            </div>
        </section>
    </main>
    <?php include 'includes/footer.php'; ?>
    <script src="js/verif_connexion.js"></script>
</body>
</html>
