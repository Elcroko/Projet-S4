<?php
session_start();

$id = isset($_GET['id']) ? basename($_GET['id']) : 'voyage01';
$nom_fichier = "json/{$id}.json";

if (!file_exists($nom_fichier)) {
    header("Location: circuits.php");
    exit;
}

$data = json_decode(file_get_contents($nom_fichier), true);
$data['id'] = $id;

// 🧠 Stocke l'ID du voyage
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

                        // 💰 Ajout du prix de l’option
                        if (isset($etape['options_disponibles'][$categorie][$choix])) {
                            $data['prix_total'] += (int) $etape['options_disponibles'][$categorie][$choix];
                        }
                    }
                }

                // 👥 personnes supplémentaires
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
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['titre']) ?></title>
    <link rel="stylesheet" href="css/voyage.css">
    <link rel="stylesheet" href="css/panier.css">
    <link rel="icon" type="image/png" href="images/portail.png">
</head>
<body class="dynamic-bg" style="background-image: url('<?= htmlspecialchars($data['image']) ?>');">

    <!-- Ajout du script JS -->
    <script src="js/voyage.js"></script>

    <!-- Détection du thème sombre -->
    <script src="js/theme.js"></script>

    <header class="header-top">
        <div class="logo-panier">
            <img src="images/portail.png" alt="Logo Tempus Odyssey" class="logo">
            <?php include 'panier.php'; ?>
        </div>
        <h1 class="site-title">
            <a href="index.php" style="text-decoration: none; color: inherit;">Tempus Odyssey</a>
        </h1>

        <button id="theme-toggle" class="btn">Changer de thème</button>

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
                <?php foreach ($data as $cle => $etape): ?>
                    <?php if (preg_match('/^etape\d+$/', $cle)): ?>
                        <div class="step">
                            <h4><?= ucfirst($cle) ?></h4>
                            <?php foreach ($etape[0]['options_disponibles'] as $type => $options): ?>
                                <label><?= ucfirst($type) ?> :</label>
                                <select name="options[<?= $cle ?>][0][<?= $type ?>]">
                                    <?php foreach ($options as $option => $prix): ?>
                                        <option value="<?= htmlspecialchars($option) ?>">
                                            <?= htmlspecialchars($option) ?> <?= $prix > 0 ? '(+' . $prix . '€)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
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

</body>
</html>
