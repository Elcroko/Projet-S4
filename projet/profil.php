<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'verif_banni.php';

// Vérification de l'authentification
if (!isset($_SESSION['user'])) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Utilisateur non authentifié.', 'redirect' => 'connexion.php']);
        exit;
    } else {
        header("Location: connexion.php");
        exit;
    }
}

$file = 'json/utilisateurs.json';
$is_ajax_request = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['user']['id'];
    
    if (!file_exists($file)) {
        if ($is_ajax_request) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erreur système: Fichier de données introuvable.']);
            exit;
        }
        die('Erreur système: Fichier de données introuvable.');
    }

    $users_data = file_get_contents($file);
    if ($users_data === false) {
        if ($is_ajax_request) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erreur: Impossible de lire le fichier utilisateurs.']);
            exit;
        }
        die('Erreur: Impossible de lire le fichier utilisateurs.');
    }

    $users = json_decode($users_data, true);
    if ($users === null) {
         if ($is_ajax_request) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erreur: Format de données invalide.']);
            exit;
        }
        die('Erreur: Format de données invalide.');
    }

    $user_found_and_updated = false;
    $updated_user_data_for_session = [];

    foreach ($users as &$user_record) { // Utiliser une référence pour modifier directement le tableau
        if ($user_record['id'] == $id) {
            // Récupérer et valider les données postées
            $nom = isset($_POST['nom']) ? trim($_POST['nom']) : $user_record['nom'];
            $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : $user_record['prenom'];
            $email = isset($_POST['email']) ? trim(strtolower($_POST['email'])) : $user_record['email'];
            $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : $user_record['telephone'];

            // Validation basique (vous devriez étendre cela)
            if (empty($nom) || !preg_match("/^[a-zA-ZÀ-ÿ\s-]+$/i", $nom)) { $error_msg = 'Nom invalide.'; break; }
            if (empty($prenom) || !preg_match("/^[a-zA-ZÀ-ÿ\s-]+$/i", $prenom)) { $error_msg = 'Prénom invalide.'; break; }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error_msg = 'Email invalide.'; break; }
            if (!preg_match("/^[0-9]{10}$/", $telephone)) { $error_msg = 'Téléphone invalide (10 chiffres attendus).'; break; }
            
            $user_record['nom'] = $nom;
            $user_record['prenom'] = $prenom;
            $user_record['email'] = $email; 
            $user_record['telephone'] = $telephone;
            
            $user_found_and_updated = true;
            
            // Préparer les données pour la session et la réponse AJAX
            $updated_user_data_for_session = [
                'id' => $id, // Conserver l'id
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'date_naissance' => $user_record['date_naissance'] // Conserver la date de naissance de la session
                // Ajoutez d'autres champs de session si nécessaire
            ];
             $_SESSION['user'] = $updated_user_data_for_session; // Mettre à jour la session immédiatement
            break;
        }
    }
    unset($user_record); // Casser la référence

    if (isset($error_msg)) { // Si une erreur de validation est survenue
        if ($is_ajax_request) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $error_msg]);
            exit;
        } else {
            $_SESSION['error_message_profil'] = $error_msg;
            header("Location: profil.php");
            exit;
        }
    }

    if ($user_found_and_updated) {
        if (file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            if ($is_ajax_request) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => 'Profil mis à jour avec succès!', 
                    'user' => [ // Renvoyer les champs mis à jour
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'email' => $email,
                        'telephone' => $telephone
                    ]
                ]);
                exit;
            } else {
                header("Location: profil.php"); // Rechargement simple pour non-AJAX
                exit;
            }
        } else { // Erreur de sauvegarde fichier
            if ($is_ajax_request) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la sauvegarde des modifications.']);
                exit;
            } else {
                 $_SESSION['error_message_profil'] = 'Erreur lors de la sauvegarde des modifications.';
                 header("Location: profil.php");
                 exit;
            }
        }
    } elseif (!$is_ajax_request) { // Si non AJAX et utilisateur non trouvé (ne devrait pas arriver si session est OK)
        $_SESSION['error_message_profil'] = 'Utilisateur non trouvé pour la mise à jour.';
        header("Location: profil.php");
        exit;
    } elseif ($is_ajax_request) { // Si AJAX et utilisateur non trouvé
         header('Content-Type: application/json');
         echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé pour la mise à jour.']);
         exit;
    }
}

// Le reste du code PHP pour afficher la page (quand ce n'est pas une requête POST AJAX)
$users_data_display = file_get_contents($file);
$users_display = json_decode($users_data_display, true);
$current_user_id_display = $_SESSION['user']['id']; // Utiliser l'id de la session
$user_to_display = null;

if ($users_display) {
    foreach ($users_display as $u_disp) {
        if ($u_disp['id'] == $current_user_id_display) {
            $user_to_display = $u_disp;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<!DOCTYPE html>
<html lang="fr">
<?php include 'includes/head.php'; ?>

<body>
<?php include 'includes/header.php'; ?>
    
    <main>
        <div class="main-container">
            <?php if ($user_to_display): ?>
                <section class="profil-container">
                    <h2>Informations personnelles</h2>
                    <div id="profil-message-area"></div> 
                    <?php 
                    // Afficher les messages d'erreur pour les POST non-AJAX
                    if (isset($_SESSION['error_message_profil'])) {
                        echo '<div class="profil-message error">' . htmlspecialchars($_SESSION['error_message_profil']) . '</div>';
                        unset($_SESSION['error_message_profil']);
                    }
                    ?>
                    <form id="profil-form" class="profil-form">
                        <div class="form-group">
                            <label for="nom">Nom :</label>
                            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user_to_display['nom']) ?>" required readonly>
                            <button type="button" class="edit-btn" data-field="nom">Modifier</button>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom :</label>
                            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user_to_display['prenom']) ?>" required readonly>
                            <button type="button" class="edit-btn" data-field="prenom">Modifier</button>
                        </div>
                        <div class="form-group">
                            <label for="email">Email :</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_to_display['email']) ?>" required readonly>
                            <button type="button" class="edit-btn" data-field="email">Modifier</button>
                        </div>
                        <div class="form-group">
                            <label for="telephone">Téléphone :</label>
                            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($user_to_display['telephone'] ?? '') ?>" required readonly>
                            <button type="button" class="edit-btn" data-field="telephone">Modifier</button>
                        </div>
                        <div class="form-group">
                            <label for="date_naissance">Date de naissance : </label>
                            <input type="date" id="date_naissance" name="date_naissance"
                                value="<?= htmlspecialchars($user_to_display['date_naissance']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="date_inscription">Date d'inscription :</label>
                            <input type="text" name="date_inscription" value="<?= htmlspecialchars($user_to_display['date_inscription']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nombre_voyages">Voyages réservés :</label>
                            <input type="number" name="nombre_voyages" value="<?= htmlspecialchars($user_to_display['nombre_voyages']) ?>" readonly>
                        </div>
                        <button type="submit" id="save-changes" class="save-btn" style="display: none;">Enregistrer les modifications</button>
                    </form>
                </section>
            <?php else: ?>
                <p>Utilisateur introuvable.</p>
            <?php endif; ?>

            <section class="historique">
                <h2>Historique des voyages</h2>
                <?php $historique = $user_to_display['historique'] ?? []; ?>
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
    <?php include 'includes/footer.php'; ?>
    <script src="js/panier.js"></script>
    <script src="js/profil.js"></script>
</body>
</html>
