<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$file = 'json/utilisateurs.json';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Tous les champs sont requis !";

    } elseif (!file_exists($file)) {
            $message = "Aucun utilisateur enregistré.";
            
    } else {
            $data = file_get_contents($file);
            $users = json_decode($data, true);

            $found = false;

            foreach ($users as $user) {
                    if ($user['email'] === $email && password_verify($password, $user['mot_de_passe'])) {
                        $_SESSION['user'] = [
                            "id" => $user['id'],
                            "nom" => $user['nom'],
                            "prenom" => $user['prenom'],
                            "email" => $user['email'],
                            'date_naissance' => $user['date_naissance']
                        ];
                        $_SESSION['role'] = $user['admin'] === true ? 'admin' : 'user';

                        header("Location: profil.php");
                        exit;
                    }
                
            }
        
        $message = "Email ou mot de passe incorrect.";
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
            <h2>Connexion</h2>
            <p>Connectez-vous pour accéder à vos voyages temporels !</p>
            
            <?php if (!empty($message)): ?>
                <p style="color: red;"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form action="connexion.php" method="POST">
                <input type="email" id="email" name="email" style="text-transform: lowercase;" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>
            </form>
            <p>
                <p>
                    <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                </p>
            </p>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>
</body>
</html>
