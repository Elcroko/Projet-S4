<?php
session_start();

function chargerVoyages() {
    $voyages = [];
    foreach (glob("json/voyage*.json") as $file) {
        $content = file_get_contents($file);
        $voyage = json_decode($content, true);
        if ($voyage) {
            $voyages[] = $voyage;
        }
    }
    return $voyages;
}

$tousVoyages = chargerVoyages();
$voyagesFiltres = $tousVoyages;

if (isset($_GET['recherche']) && isset($_GET['btn_rechercher'])) {
    $mot = strtolower(trim($_GET['recherche']));
    $voyagesFiltres = [];

    foreach ($tousVoyages as $voyage) {
        $titre = strtolower($voyage['titre'] ?? '');
        $description = strtolower($voyage['description'] ?? '');

        if (strpos($titre, $mot) !== false || strpos($description, $mot) !== false) {
            $voyagesFiltres[] = $voyage;
        }
    }
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$voyagesParPage = 6;
$totalVoyages = count($voyagesFiltres);
$totalPages = ceil($totalVoyages / $voyagesParPage);
$offset = ($page - 1) * $voyagesParPage;
$voyagesPage = array_slice($voyagesFiltres, $offset, $voyagesParPage);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Circuits - Tempus Odyssey</title>
    <link rel="icon" type="image/png" href="images/portail.png">
    <link rel="stylesheet" href="css/circuits.css">
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
        <section class="filters">
            <h2>Recherchez votre voyage temporel</h2>
            <form method="get">
                <input class="search-bar" type="text" name="recherche" placeholder="Rechercher un circuit...">
                <button class="btn" type="submit" name="btn_rechercher">Rechercher</button>
            </form>
        </section>     
        
        <form method="get" class="filtres-voyages">
            <label for="epoque">Époque :</label>
            <select name="epoque" id="epoque">
                <option value="">Toutes</option>
                <option value="préhistoire">Préhistoire</option>
                <option value="antiquité">Antiquité</option>
                <option value="moyen-age">Moyen-Âge</option>
                <option value="renaissance">Renaissance</option>
                <option value="temps-modernes">Temps modernes</option>
                <option value="futur">Futur</option>
            </select>

            <label for="lieu">Lieu :</label>
            <select name="lieu" id="lieu">
                <option value="">Tous</option>
                <option value="europe">Europe</option>
                <option value="asie">Asie</option>
                <option value="afrique">Afrique</option>
                <option value="amerique">Amérique</option>
                <option value="espace">Espace</option>
            </select>

            <label for="prix">Prix :</label>
            <select name="prix" id="prix">
                <option value="">Tous</option>
                <option value="1">Moins de 1000 €</option>
                <option value="2">Entre 1000 € et 2000 €</option>
                <option value="3">Plus de 2000 €</option>
            </select>

            <button type="submit" class="btn">Filtrer</button>
        </form>
        
        <!-- Section des circuits temporels -->
        <section class="featured">
            <h3>Nos circuits temporels</h3>
            <div class="circuits-container">
            <?php if (count($voyagesPage) === 0): ?>
                <p class="no-result">Aucun résultat pour cette recherche.</p>
            <?php else: ?>
                <?php foreach ($voyagesPage as $index => $voyage): ?>
                    <a href="voyage.php?id=<?= 'voyage' . str_pad($index + 1 + $offset, 2, '0', STR_PAD_LEFT) ?>" class="circuit-link">
                        <article class="circuit">
                            <img src="<?= htmlspecialchars($voyage['image']) ?>" alt="Illustration du circuit <?= htmlspecialchars($voyage['titre']) ?>" />
                            <h4><?= htmlspecialchars($voyage['titre']) ?></h4>
                            <p><?= htmlspecialchars($voyage['description']) ?></p>

                            <?php if (isset($voyage['dates']['duree'])): ?>
                                <p><strong>Durée :</strong> <?= htmlspecialchars($voyage['dates']['duree']) ?></p>
                            <?php endif; ?>

                            <?php if (isset($voyage['prix_base'])): ?>
                                <p><strong>A partir de </strong> <?= htmlspecialchars($voyage['prix_base']) ?> €</p>
                            <?php endif; ?>

                            <?php
                            $nbEtapes = 0;
                            foreach ($voyage as $cle => $valeur) {
                                if (str_starts_with($cle, 'etape')) $nbEtapes++;
                            }
                            ?>
                            <p><strong>Nombre d’étapes :</strong> <?= $nbEtapes ?></p>
                        </article>
                    </a>

                <?php endforeach; ?>
            <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" <?= $i === $page ? 'style="font-weight: bold;"' : '' ?>>
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
</body>
</html>
