<?php
require_once 'verif_banni.php';

$page_title = "Circuits - Tempus Odyssey";
$css_file = "circuits.css";

// Chargement des voyages
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
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/header.php'; ?>
    <main>
        <section class="filters">
            <h2>Recherchez votre voyage temporel</h2>
            <form method="get">
                <input class="search-bar" type="text" name="recherche" placeholder="Rechercher un circuit...">
                <button class="btn_search" type="submit" name="btn_rechercher">Rechercher</button>
            </form>
        </section>     
        
        <form id="filters-form" class="filtres-voyages">
            <label for="epoque">Époque :</label>
            <select id="epoque">
                <option value="">Toutes</option>
                <option value="Préhistoire">Préhistoire</option>
                <option value="Antiquité">Antiquité</option>
                <option value="Moyen-Âge">Moyen-Âge</option>
                <option value="Renaissance">Renaissance</option>
                <option value="Temps modernes">Temps modernes</option>
                <option value="Contemporaine">Contemporaine</option>
                <option value="Futur">Futur</option>
            </select>

            <label for="lieu">Lieu :</label>
            <select id="lieu">
                <option value="">Tous</option>
                <option value="europe">Europe</option>
                <option value="asie">Asie</option>
                <option value="afrique">Afrique</option>
                <option value="amerique">Amérique</option>
                <option value="espace">Espace</option>
            </select>

            <label for="prix">Prix :</label>
            <select id="prix">
                <option value="">Tous</option>
                <option value="1">Moins de 1000 €</option>
                <option value="2">Entre 1000 € et 2000 €</option>
                <option value="3">Plus de 2000 €</option>
            </select>

        </form>
        
        <!-- Section des circuits temporels -->
        <section class="featured">
            <h3>Nos circuits temporels</h3>
            <div class="tri-container">
                <label for="sort-by">Trier par :</label>
                <select id="sort-by">
                    <option value="">-- Choisir --</option>
                    <option value="prix">Prix</option>
                    <option value="duree">Durée</option>
                    <option value="etapes">Nombre d'étapes</option>
                </select>
            </div>
            
            <!-- Conteneur visible avec la pagination PHP -->
            <?php ob_start(); ?>
            <div class="circuits-container">
            <?php if (count($voyagesPage) === 0): ?>
                <p class="no-result">Aucun résultat pour cette recherche.</p>
            <?php else: ?>
                <?php foreach ($voyagesPage as $index => $voyage): ?>
                    <a href="voyage.php?id=<?= 'voyage' . str_pad($index + 1 + $offset, 2, '0', STR_PAD_LEFT) ?>" 
                        class="circuit-link"
                        data-epoque="<?= htmlspecialchars(strtolower($voyage['epoque'] ?? '')) ?>"
                        data-lieu="<?= htmlspecialchars(strtolower($voyage['lieu'] ?? '')) ?>"
                        data-prix="<?= htmlspecialchars($voyage['prix_base'] ?? 0) ?>">

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
            <?php $originalCircuits = ob_get_clean(); ?>
            <?= $originalCircuits ?>
            <div id="original-circuits" style="display: none;">
                <?= $originalCircuits ?>
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

            <!-- ⬇️ CONTENEUR MASQUÉ POUR JS (TOUS LES VOYAGES) -->
            <div id="js-voyages" style="display: none;">
            <?php foreach ($tousVoyages as $index => $voyage): ?>
                <a href="voyage.php?id=<?= 'voyage' . str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?>" 
                    class="circuit-link"
                    data-epoque="<?= htmlspecialchars(strtolower($voyage['epoque'] ?? '')) ?>"
                    data-lieu="<?= htmlspecialchars(strtolower($voyage['lieu'] ?? '')) ?>"
                    data-prix="<?= htmlspecialchars($voyage['prix_base'] ?? 0) ?>">

                    <article class="circuit">
                        <img src="<?= htmlspecialchars($voyage['image']) ?>" alt="Illustration du circuit <?= htmlspecialchars($voyage['titre']) ?>" />
                        <h4><?= htmlspecialchars($voyage['titre']) ?></h4>
                        <p><?= htmlspecialchars($voyage['description']) ?></p>

                        <?php if (isset($voyage['dates']['duree'])): ?>
                            <p><strong>Durée :</strong> <?= htmlspecialchars($voyage['dates']['duree']) ?></p>
                        <?php endif; ?>

                        <p><strong>A partir de </strong> <?= htmlspecialchars($voyage['prix_base']) ?> €</p>

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
            </div>
        </section>
    </main>
    <?php include 'includes/footer.php'; ?>
    <script src="js/panier.js"></script>
    <script src="js/circuits.js"></script>
</body>
</html>
