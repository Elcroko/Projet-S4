<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit;
}


// ↓ Bloc ajouté pour gérer la sauvegarde des modifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['user']['id'];
    $file = 'json/utilisateurs.json';
    $users = json_decode(file_get_contents($file), true);

    // Mise à jour des champs modifiables
    foreach ($users as &$user) {
        if ($user['id'] == $id) {
            $user['nom'] = $_POST['nom'];
            $user['prenom'] = $_POST['prenom'];
            $user['email'] = $_POST['email'];
            $user['telephone'] = $_POST['telephone'];
            break;
        }
    }

    // Sauvegarde dans le fichier JSON
    file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

    // Mise à jour de la session
    $_SESSION['user']['nom'] = $_POST['nom'];
    $_SESSION['user']['prenom'] = $_POST['prenom'];
    $_SESSION['user']['email'] = $_POST['email'];
}

// Charger les infos utilisateur
$file = 'json/utilisateurs.json';
$users = json_decode(file_get_contents($file), true);
$id = $_SESSION['user']['id'];
$user = null;

foreach ($users as $u) {
    if ($u['id'] == $id) {
        $user = $u;
        break;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Tempus Odyssey</title>
    <link rel="stylesheet" href="css/profil.css">
    <link rel="stylesheet" href="css/panier.css">
</head>
<body>
    <!-- Ajout du script JS -->
    <script src="js/profil.js"></script>

    <!-- Détection du thème sombre -->
    <script src="js/theme.js"></script>
    
    <!-- En-tête -->
    <header class="header-top">
        <div class="logo-panier">
            <img src="images/portail.png" alt="Logo Tempus Odyssey" class="logo">
            <?php include 'panier.php'; ?>
        </div>
        
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
    
    <!-- Contenu principal -->
    <main>
        <?php
        $file = 'json/utilisateurs.json';
        $users = json_decode(file_get_contents($file), true);
        $id = $_SESSION['user']['id'];
        $user = null;

        foreach ($users as $u) {
            if ($u['id'] == $id) {
                $user = $u;
                break;
            }
        }
        ?>
        <div class="main-container">
            <?php if ($user): ?>
                <section class="profil-container">
                    <h2>Informations personnelles</h2>
                    <form method="post" action="profil.php" class="profil-form">
                    
                    <div class="form-group">
                        <label for="nom">Nom :</label>
                        <input type="text" id= "nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required readonly>
                        <button type="button" class="edit-btn" data-field="nom">Modifier</button>
                    </div>

                    <div class="form-group">
                        <label for="prenom">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required readonly>
                        <button type="button" class="edit-btn" data-field="prenom">Modifier</button>
                    </div>

                    <div class="form-group">
                        <label for="email">Email :</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required readonly>
                        <button type="button" class="edit-btn" data-field="email">Modifier</button>
                    </div>

                    <div class="form-group">
                        <label for="telephone">Téléphone :</label>
                        <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" required readonly>
                        <button type="button" class="edit-btn" data-field="telephone">Modifier</button>
                    </div>

                    <div class="form-group">
                        <label for="date_naissance">Date de naissance : </label>
                        <input type="date" id="date_naissance" name="date_naissance"
                            value="<?= htmlspecialchars($user['date_naissance']) ?>" readonly>
                    </div>


                    <div class="form-group">
                        <label for="date_inscription">Date d'inscription :</label>
                        <input type="text" name="date_inscription" value="<?= htmlspecialchars($user['date_inscription']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="nombre_voyages">Voyages réservés :</label>
                        <input type="number" name="nombre_voyages" value="<?= htmlspecialchars($user['nombre_voyages']) ?>" readonly>
                    </div>
                        <button type="submit" id="save-changes" class="save-btn" style="display: none;">Enregistrer les modifications</button>
                    </form>
                </section>
            <?php else: ?>
                <p>Utilisateur introuvable.</p>
            <?php endif; ?>

            <section class="historique">
                <h2>Historique des voyages</h2>
                <?php
                $data = file_get_contents('json/utilisateurs.json');
                $utilisateurs = json_decode($data, true);
                $historique = [];

                foreach ($utilisateurs as $user) {
                    if ($user['email'] === $_SESSION['user']['email']) {
                        $historique = $user['historique'] ?? [];
                        break;
                    }
                }
                ?>

                <?php if (empty($historique)): ?>
                    <p>Aucun voyage encore effectué.</p>
                <?php else: ?>
                    <ul>
                    <?php foreach ($historique as $item): ?>
                        <li>
                            <strong><?= htmlspecialchars($item['titre']) ?></strong> —
                            <?= htmlspecialchars($item['prix']) ?> €
                            <a href="recapitulatif.php?fichier=<?= urlencode($item['fichier_commande']) ?>">Voir le récapitulatif</a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>

    </main>

    <!-- Pied de page -->
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer>

</body>
</html>