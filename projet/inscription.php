<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start(); 
session_start();


$file = 'json/utilisateurs.json';
$erreurs = [];

// Créer le fichier s'il n'existe pas
if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = strtolower($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirm_mdp = $_POST['confirm_mdp'];
    $date_naissance = $_POST['date_naissance'];
    $telephone = $_POST['telephone'];
    $terms = isset($_POST['terms']) ? true : false;


    // Vérification de la date de naissance
    $birthDate = DateTime::createFromFormat('Y-m-d', $date_naissance);
    $today = new DateTime();

    if (!$birthDate || $birthDate > $today) {
        $erreurs[] = "La date de naissance est invalide.";
    } else {
        $age = $today->diff($birthDate)->y;
        if ($age < 18) {
            $erreurs[] = "Vous devez avoir au moins 18 ans pour vous inscrire.";
        }
    }

    // Vérifier si tous les champs sont remplis
    if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe) || empty($confirm_mdp) || empty($date_naissance) || empty($telephone)) {
        $message = "Tous les champs sont requis !";
    } elseif ($mot_de_passe !== $confirm_mdp) {
        $message = "Les mots de passe ne correspondent pas !";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email invalide !";
    } else {
        if (empty($erreurs)){
            // Hacher le mot de passe 
            $mdp_hacher = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Charger les utilisateurs existants depuis le fichier JSON
            $data = file_get_contents($file);
            $users = json_decode($data, true);

            // Vérifier si l'email existe déjà
            $emailExists = false;
            foreach ($users as $user) {
                if ($user['email'] === $email) {
                    $emailExists = true;
                    break;
                }
            }

            if ($emailExists) {
                $message = "Cet email est déjà utilisé !";
            } else {
                // Créer un nouvel utilisateur
                $newUser = [
                    "id" => count($users) + 1,
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "email" => $email,
                    "mot_de_passe" => $mdp_hacher,
                    "date_naissance" => $date_naissance,
                    "date_inscription" => date("d/m/Y"),
                    "admin" => false,
                    "nombre_voyages" => 0,
                    "telephone" => $telephone
                ];

                // Ajouter le nouvel utilisateur au tableau
                $users[] = $newUser;

            // Enregistrer les données mises à jour dans le fichier JSON
            if (file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT)) === false) {
                $message = "Erreur lors de l'enregistrement des données !";
            } else {
                $_SESSION['user'] = [
                    'id' => $newUser['id'],
                    'nom' => $newUser['nom'],
                    'prenom' => $newUser['prenom'],
                    'email' => $newUser['email']
                ];
                $_SESSION['role'] = $newUser['admin'] === true ? 'admin' : 'user';

                header("Location: index.php");
                exit;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Tempus Odyssey</title>
  
    <link rel="icon" type="image/png" href="images/portail.png">
    <link rel="stylesheet" href="css/inscription.css">
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

    <main>
        <section class="form-container">
            <h2>Inscription</h2>
            <p>Rejoignez-nous pour explorer les voyages temporels !</p>

            <?php if (!empty($erreurs)): ?>
                <div class="erreurs">
                    <?php foreach ($erreurs as $err): ?>
                        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($err) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="inscription.php" method="POST">
                <input type="text" id="nom" name="nom" placeholder="Nom" required>
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>
                <input type="email" id="email" name="email" placeholder="Email" required style="text-transform: lowercase;">
                <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Mot de passe" required>
                <input type="password" id="confirm_mdp" name="confirm_mdp" placeholder="Confirmer le mot de passe" required>
                <input type="date" name="date_naissance" max="<?= date('Y-m-d') ?>">
                <input type="tel" name="telephone" placeholder="Téléphone" required>
                <div class="checkbox-container">
                    <input type="checkbox" id="terms" name="terms" required>
                    <p>J'accepte les <a href="termes.html">termes et conditions</a></p>
                </div>
                <button type="submit">S'inscrire</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>
</body>
</html>
<?php ob_end_flush();  ?>
