<?php
ob_start(); // Démarre la mise en tampon de sortie
session_start();

// Définir le chemin du fichier JSON
$file = 'json/utilisateurs.json';

// Vérifier si le fichier JSON existe, sinon le créer
if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

// Variable pour afficher un message après l'inscription
$message = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $birthdate = $_POST['birthdate'];
    $terms = isset($_POST['terms']) ? true : false;

    // Vérifier si tous les champs sont remplis
    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirm_password) || empty($birthdate)) {
        $message = "Tous les champs sont requis !";
    } elseif ($password !== $confirm_password) {
        $message = "Les mots de passe ne correspondent pas !";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email invalide !";
    } else {
        // Hacher le mot de passe pour la sécurité
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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
                "mot_de_passe" => $hashed_password,
                "date_de_naissance" => $birthdate,
                "accepte_conditions" => $terms,
                "date_inscription" => date("Y-m-d H:i:s")
            ];

            // Ajouter le nouvel utilisateur au tableau
            $users[] = $newUser;

           // Enregistrer les données mises à jour dans le fichier JSON
           if (file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT)) === false) {
            $message = "Erreur lors de l'enregistrement des données !";
        } else {
            // Rediriger vers la page de confirmation après inscription
            header("Location: confirmation.php");
            exit;
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
            </ul>
        </nav>
    </header>

    <main>
        <section class="form-container">
            <h2>Inscription</h2>
            <p>Rejoignez-nous pour explorer les voyages temporels !</p>

            <?php if (!empty($message)): ?>
                <p style="color: <?= strpos($message, 'réussie') !== false ? 'green' : 'red' ?>;"><?= $message ?></p>
            <?php endif; ?>

            <form action="inscription.php" method="POST">
                <input type="text" id="nom" name="nom" placeholder="Nom" required>
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirmer le mot de passe" required>
                <input type="date" id="birthdate" name="birthdate" required>
                <div class="checkbox-container">
                    <input type="checkbox" id="terms" name="terms" required>
                    <p>J'accepte les <a href="termes.html" target="_blank">termes et conditions</a></p>
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
<?php ob_end_flush(); // Envoie le contenu du tampon de sortie ?>
