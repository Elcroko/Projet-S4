<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$file = 'json/utilisateurs.json';

if (!file_exists($file)) {
    echo "Fichier utilisateur introuvable.";
    exit;
}

// Si un admin clique sur le bouton pour changer le rôle d'un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_admin']) && isset($_POST['email'])) {
    $email = $_POST['email'];
    $users = json_decode(file_get_contents($file), true);

    foreach ($users as &$user) {
        if ($user['email'] === $email && $user['email'] !== $_SESSION['user']['email']) {
            $user['admin'] = !$user['admin']; // inverse le rôle admin
            break;
        }
    }

    file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

$users = json_decode(file_get_contents($file), true);

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$usersPerPage = 5;
$totalUsers = count($users);
$totalPages = ceil($totalUsers / $usersPerPage);
$offset = ($page - 1) * $usersPerPage;
$usersPage = array_slice($users, $offset, $usersPerPage);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="css/admin.css">
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
            <li><a href="admin.php">Admin</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="admin-container">
        <h2>Liste des Utilisateurs</h2>
        <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Date de naissance</th>
                <th>Date d'inscription</th>
                <th>Téléphone</th>
                <th>Voyages</th>
                <th>Admin</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usersPage as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['date_naissance']) ?></td>
                    <td><?= htmlspecialchars($user['date_inscription']) ?></td>
                    <td><?= htmlspecialchars($user['telephone']) ?></td>
                    <td><?= htmlspecialchars($user['nombre_voyages']) ?></td>
                    <td><?= $user['admin'] ? 'Oui' : 'Non' ?></td>
                    <td>
                    <?php if ($user['email'] !== $_SESSION['user']['email']): ?>
                        <form method="POST" action="admin.php" style="display:inline;">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                            <button type="submit" name="toggle_admin" class="admin-btn">
                                <?= $user['admin'] ? 'Retirer Admin' : 'Rendre Admin' ?>
                            </button>
                        </form>
                        <?php else: ?>
                            <em>Vous</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
           <?= $i === $page ? 'style="font-weight: bold;"' : '' ?>>
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

    </main>   
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer> 
</body>
</html>
