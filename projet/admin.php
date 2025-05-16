<?php
$page_title = "Admin - Gestion des Utilisateurs";
require_once 'verif_banni.php';

if (!isset($_SESSION['user']) || empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: connexion.php");
    exit;
}

$file = 'json/utilisateurs.json'; 

if (!file_exists($file)) {
    echo "Fichier utilisateur introuvable.";
    exit;
}

// Gestion spéciale des requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    header('Content-Type: application/json');

    $email = $_POST['email'];
    $users = json_decode(file_get_contents($file), true);
    $found = false;
    $changement = [];

    foreach ($users as &$user) {
        if ($user['email'] === $email && $user['email'] !== $_SESSION['user']['email']) {

            // Traitement admin
            if (isset($_POST['admin'])) {
                $newAdminStatus = intval($_POST['admin']);
                $user['admin'] = $newAdminStatus === 1;
                $changement[] = 'admin';
            }

            // Traitement banni
            if (isset($_POST['banni'])) {
                $newBanStatus = intval($_POST['banni']);
                $user['banni'] = $newBanStatus === 1;
                $changement[] = 'banni';

                // Si l'utilisateur est banni, on lui retire les droits admin automatiquement
                if ($newBanStatus === 1 && !empty($user['admin'])) {
                    $user['admin'] = false;
                    $changement[] = 'admin (révoqué)';
                }
            }

            $found = true;
            break;
        }
    }

    if ($found) {
        $success = file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        if ($success === false) {
            echo json_encode(['success' => false, 'message' => "Erreur lors de la sauvegarde."]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => "Changement(s) appliqué(s) à $email : " . implode(', ', $changement)
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Utilisateur non trouvé ou modification non autorisée."]);
    }

    exit;
}

// --- Si on arrive ici, c'est qu'on veut afficher la page normale ---

$users = json_decode(file_get_contents($file), true);
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$usersPerPage = 5;
$totalUsers = count($users);
$totalPages = ceil($totalUsers / $usersPerPage);
$offset = ($page - 1) * $usersPerPage;
$usersPage = array_slice($users, $offset, $usersPerPage);
?>

<!DOCTYPE html>
<html lang="fr">
<?php include 'includes/head.php'; ?>

<body>
    <!-- En-tête -->
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="admin-container">
                <h2>Liste des Utilisateurs</h2>
                <div class="table-responsive">
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
                            <th>Banni</th>
                            <th>Actions</th>
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
                                <td class="admin-status"><?= !empty($user['admin']) ? 'Oui' : 'Non' ?></td>
                                <td class="ban-status"><?= !empty($user['banni']) ? 'Oui' : 'Non' ?></td>
                                <td>
                                <?php if ($user['email'] !== $_SESSION['user']['email']): ?>
                                    <button type="button" 
                                            class="admin-btn rendre-admin-btn" 
                                            data-admin="<?= !empty($user['admin']) ? '1' : '0' ?>" 
                                            data-email="<?= htmlspecialchars($user['email']) ?>">
                                        <?= !empty($user['admin']) ? 'Retirer admin' : 'Rendre admin' ?>
                                    </button>

                                    <?php if (!empty($user['admin'])): ?>
                                        <button class="admin-btn bannir-btn disabled-btn" disabled title="Impossible de bannir un admin">
                                            Admin protégé
                                        </button>
                                    <?php else: ?>
                                        <button class="admin-btn bannir-btn" data-email="<?= htmlspecialchars($user['email']) ?>">
                                            <?= !empty($user['banni']) ? 'Débannir' : 'Bannir' ?>
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <em>Vous</em>
                                <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>
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
        </section>
    </main>   
    <footer>
        <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
    </footer> 
    <script src="js/panier.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>
