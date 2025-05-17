<?php
// 🔁 Bloc AJAX pour chargement dynamique des options
if (isset($_GET['options'])) {
    header('Content-Type: application/json');

    function convertir($groupe) {
        $liste = [];
        foreach ($groupe as $nom => $prix) {
            $liste[] = [
                'nom' => $nom,
                'valeur' => strtolower(preg_replace('/\s+/', '_', $nom)),
                'prix' => $prix
            ];
        }
        return $liste;
    }

    $id = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['options']);
    $fichier = "json/{$id}.json";

    if (!file_exists($fichier)) {
        echo json_encode(['success' => false, 'message' => 'Fichier introuvable']);
        exit;
    }

    $data = json_decode(file_get_contents($fichier), true);
    $etapes = [];

    foreach ($data as $cle => $val) {
        if (preg_match('/^etape[0-9]+$/', $cle)) {
            foreach ($val as $etape) {
                $opt = $etape['options_disponibles'] ?? [];
                $etapes[] = [
                    'position' => convertir($opt['position'] ?? []),
                    'activite' => convertir($opt['activite'] ?? []),
                    'hebergement' => convertir($opt['hebergement'] ?? []),
                    'restauration' => convertir($opt['restauration'] ?? []),
                    'transport' => convertir($opt['transport'] ?? [])
                ];
            }
        }
    }

    echo json_encode(['etapes' => $etapes]);
    exit;
}

// 🌐 Traitement standard de la page
require_once 'verif_banni.php';

$id = isset($_GET['id']) ? basename($_GET['id']) : 'voyage01';
$nom_fichier = "json/{$id}.json";

if (!file_exists($nom_fichier)) {
    header("Location: circuits.php");
    exit;
}

$data = json_decode(file_get_contents($nom_fichier), true);
$data['id'] = $id;
$_SESSION['recapitulatif_id'] = $id;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
    $data['date_depart'] = $_POST['date_depart'] ?? null;
    $data['duree'] = $data['duree'] ?? 10;
    $data['nombre_personnes'] = $_POST['nombre_personnes'] ?? 0;
    $data['personnes'] = $_POST['personnes'] ?? [];
    $data['prix_total'] = $data['prix_base'];

    foreach ($data as $cle => &$etapes) {
        if (strpos($cle, 'etape') === 0 && is_array($etapes)) {
            foreach ($etapes as $index => &$etape) {
                $etape['options_choisies'] = [];

                if (isset($_POST['options'][$cle][$index])) {
                    foreach ($_POST['options'][$cle][$index] as $categorie => $choix) {
                        $etape['options_choisies'][$categorie] = $choix;

                        if (isset($etape['options_disponibles'][$categorie][$choix])) {
                            $data['prix_total'] += (int) $etape['options_disponibles'][$categorie][$choix];
                        }
                    }
                }

                $etape['personnes_supplementaires'] = $_POST['personnes_supplementaires'][$cle][$index] ?? [];
            }
        }
    }

    $_SESSION['recapitulatif'] = $data;
    header("Location: recapitulatif.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php include 'includes/head.php'; ?>

<body class="dynamic-bg" style="background-image: url('<?= htmlspecialchars($data['image']) ?>');">
<?php include 'includes/header.php'; ?>

<main class="voyage-form">
    <h2><?= htmlspecialchars($data['titre']) ?></h2>
    <p><?= htmlspecialchars($data['description']) ?></p>

    <form action="voyage.php?id=<?= urlencode($id) ?>" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <div class="voyageur">
            <?php if (!empty($data['dates']['choix_date_depart'])): ?>
                <label>Date de départ</label>
                <?php
                $minDate = date('Y-m-d', strtotime('+1 day'));
                $maxDate = date('Y-m-d', strtotime('+2 years'));
                ?>
                <input type="date" name="date_depart" required min="<?= $minDate ?>" max="<?= $maxDate ?>">
            <?php endif; ?>

            <label>Nombre de voyageurs</label>
            <input type="number" name="nombre_personnes" min="1" max="8" required placeholder="Entrez un nombre de voyageurs (max. 8)">

            <h4>Voyageur principal</h4>

            <label>Nom :</label>
            <input type="text" name="personnes[0][nom]"
                   value="<?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['nom']) : '' ?>"
                   <?= isset($_SESSION['user']) ? 'readonly' : 'required' ?>
                   placeholder="Nom du voyageur">

            <label>Prénom :</label>
            <input type="text" name="personnes[0][prenom]"
                   value="<?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['prenom']) : '' ?>"
                   <?= isset($_SESSION['user']) ? 'readonly' : 'required' ?>
                   placeholder="Prénom du voyageur">

            <label>Date de naissance :</label>
            <input type="date" name="personnes[0][date_naissance]"
                   value="<?= isset($_SESSION['user']['date_naissance']) ? $_SESSION['user']['date_naissance'] : '' ?>"
                   <?= isset($_SESSION['user']) ? 'readonly' : 'required' ?>
                   placeholder="Date de naissance">
        </div>

        <h3>Étapes</h3>
        <div class="etapes-wrapper">
            <!-- Étapes chargées dynamiquement via JS -->
        </div>

        <p class="prix-total">Prix total : <strong><?= number_format($data['prix_base'], 0, ',', ' ') ?> €</strong></p>

        <?php if (isset($_SESSION['user'])): ?>
            <button class="btn-valider">Valider la personnalisation</button>
        <?php else: ?>
            <p style="color: #c1f762; font-weight: bold;">
                <a href="connexion.php" style="color: #c1f762;">Connectez-vous</a> avant de continuer.
            </p>
        <?php endif; ?>
    </form>

    <a href="circuits.php" class="retour-circuits">← Retour aux circuits</a>
</main>

<footer>
    <p>&copy; 2025 Tempus Odyssey - Traversez les âges, vivez l’histoire.</p>
</footer>
<script src="js/panier.js"></script>
<script src="js/voyage.js"></script>
</body>
</html>
