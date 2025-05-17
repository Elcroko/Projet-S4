<?php
ob_start();
require_once 'verif_banni.php';

$page_title = "Inscription - Tempus Odyssey";
$css_file = "inscription.css";

$file = 'json/utilisateurs.json';
$erreurs = [];
$email_existe = false;

if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = strtolower(trim($_POST['email']));
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirm_mdp = $_POST['confirm_mdp'];
    $date_naissance = $_POST['date_naissance'];
    $telephone = $_POST['telephone'];
    $terms = isset($_POST['terms']);

    if (!preg_match("/^[a-zA-ZÀ-ÿ\- ]+$/", $nom) || !preg_match("/^[a-zA-ZÀ-ÿ\- ]+$/", $prenom)) {
        $erreurs[] = "Le nom et le prénom doivent contenir uniquement des lettres.";
    }

    if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe) || empty($confirm_mdp) || empty($date_naissance) || empty($telephone)) {
        $erreurs[] = "Tous les champs sont requis !";
    } elseif ($mot_de_passe !== $confirm_mdp) {
        $erreurs[] = "Les mots de passe ne correspondent pas !";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "Email invalide !";
    } elseif (!$terms) {
        $erreurs[] = "Vous devez accepter les termes et conditions.";
    }

    if (empty($erreurs)) {
        $mdp_hacher = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $data = file_get_contents($file);
        $users = json_decode($data, true);

        foreach ($users as $user) {
            if (strtolower(trim($user['email'])) === $email) {
                $email_existe = true;
                break;
            }
        }

        if (!$email_existe) {
            $newUser = [
                "id" => count($users) + 1,
                "nom" => $nom,
                "prenom" => $prenom,
                "email" => $email,
                "mot_de_passe" => $mdp_hacher,
                "date_naissance" => $date_naissance,
                "date_inscription" => date("d/m/Y"),
                "admin" => false,
                "banni" => false,
                "nombre_voyages" => 0,
                "telephone" => $telephone
            ];

            $users[] = $newUser;

            if (file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT)) === false) {
                $erreurs[] = "Erreur lors de l'enregistrement des données !";
            } else {
                $_SESSION['user'] = [
                    'id' => $newUser['id'],
                    'nom' => $newUser['nom'],
                    'prenom' => $newUser['prenom'],
                    'email' => $newUser['email'],
                    'date_naissance' => $newUser['date_naissance']
                ];
                $_SESSION['role'] = $newUser['admin'] === true ? 'admin' : 'user';

                header("Location: profil.php");
                exit;
            }
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
        <section class="form-container">
            <h2>Inscription</h2>
            <p>Rejoignez-nous pour explorer les voyages temporels !</p>

            <?php if ($email_existe): ?>
                <div class="email-erreur">
                    <h2>Cette adresse email est déjà utilisée.</h2>
                    <p>Vous avez déjà un compte ? 
                        <a href="connexion.php">Connectez-vous ici</a>.
                    </p>
                </div>
            <?php else: ?>

            <?php if (!empty($erreurs)): ?>
                <div class="erreurs">
                    <?php foreach ($erreurs as $err): ?>
                        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="inscription.php" method="POST">
                <div>
                    <input type="text" id="nom" name="nom" required pattern="[a-zA-ZÀ-ÿ\- ]+" title="Lettres uniquement" placeholder="Nom">
                    <div id="nom-count" class="char-count">0/30</div>
                </div>

                <div>
                    <input type="text" id="prenom" name="prenom" required pattern="[a-zA-ZÀ-ÿ\- ]+" title="Lettres uniquement" placeholder="Prénom">
                    <div id="prenom-count" class="char-count">0/30</div>
                </div>

                <div>
                    <input type="email" id="email" name="email" placeholder="Email" required style="text-transform: lowercase;">
                </div>

                <div style="position:relative;">
                    <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Mot de passe" autocomplete="new-password">
                    <span class="eye-icon toggle-password">👁️</span>
                    <div id="mdp-count" class="char-count">0/20</div>
                    <div class="error-message"></div>
                </div>

                <div style="position:relative;">
                    <input type="password" id="confirm_mdp" name="confirm_mdp" placeholder="Confirmer le mot de passe" required>
                    <span class="eye-icon toggle-password">👁️</span>
                    <div class="error-message"></div>    
                </div>

                <div>
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" max="<?= date('Y-m-d') ?>" title="Veuillez sélectionner votre date de naissance">
                    <div class="error-message"></div>
                </div>

                <div>
                    <input type="tel" id="telephone" name="telephone" placeholder="Téléphone" required
                        pattern="^0[1-9](\d{2}){4}$"
                        title="Entrez un numéro valide (ex : 0612345678)">
                    <div class="error-message"></div>
                </div>

                <div class="checkbox-container">
                    <label for="terms">
                        <input type="checkbox" id="terms" name="terms" required title="Vous devez accepter les conditions d'utilisation">
                        J'accepte les <a href="termes.html">termes et conditions</a>
                    </label>
                </div>

                <button type="submit">S'inscrire</button>
            </form>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>

    <script src="js/verif_inscription.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>
