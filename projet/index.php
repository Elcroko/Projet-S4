<?php
$page_title = "Tempus Odyssey - Voyagez à travers le temps";
$css_file = "styLes.css";
require_once 'verif_banni.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php include 'includes/head.php'; ?>

<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Section d'introduction -->
        <section class="hero">
            <div class="hero-content">
                <h2 class="hero-title">Embarquez pour un voyage à travers le temps</h2>
                <p class="hero-subtitle">Revivez les époques légendaires et percez les mystères du futur.</p>
                <a href="circuits.php" class="btn">Explorer</a>
            </div>
        </section>

        <!-- Section de description -->
        <section class="about">
            <div class="about-container">
                <h3>Bienvenue sur Tempus Odyssey</h3>
                <p>
                    Découvrez un concept révolutionnaire de voyage temporel qui vous permet de parcourir les âges comme jamais auparavant. 
                    Que vous soyez passionné par l’histoire, curieux du futur ou en quête d’expériences inédites, nous avons conçu pour vous des circuits immersifs qui vous transporteront à travers les époques.
                </p>
                <p>
                    Au cœur de la Rome antique aux secrets du Moyen Âge, en passant par les grandes découvertes et les futurs possibles de l’humanité, 
                    nos voyages sont sécurisés, accompagnés de guides spécialisés et adaptés à tous les aventuriers du temps.
                </p>
                <p>
                    Osez l’aventure et devenez un explorateur temporel dès aujourd’hui !
                </p>
            </div>
        </section>

        <!-- Section des circuits temporels -->
        <section class="featured">
            <h3>Nos Voyages les Plus Demandés</h3>
            <div class="circuits-container">
                <?php
                $topVoyages = [];
                for ($i = 10; $i <= 15; $i++) {
                    $fichier = "json/voyage" . str_pad($i, 2, '0', STR_PAD_LEFT) . ".json";
                    if (file_exists($fichier)) {
                        $data = json_decode(file_get_contents($fichier), true);
                        if ($data) {
                            $data['id'] = "voyage" . str_pad($i, 2, '0', STR_PAD_LEFT);
                            $topVoyages[] = $data;
                        }
                    }
                }

                foreach ($topVoyages as $voyage): ?>
                    <a href="voyage.php?id=<?= htmlspecialchars($voyage['id']) ?>" class="circuit-link">
                        <div class="circuit">
                            <img src="<?= htmlspecialchars($voyage['image']) ?>" alt="Illustration du circuit <?= htmlspecialchars($voyage['titre']) ?>" />
                            <h4><?= htmlspecialchars($voyage['titre']) ?></h4>
                            <p><?= htmlspecialchars($voyage['description']) ?></p>

                            <?php if (isset($voyage['dates']['duree'])): ?>
                                <p><strong>Durée :</strong> <?= htmlspecialchars($voyage['dates']['duree']) ?></p>
                            <?php endif; ?>

                            <?php if (isset($voyage['prix_base'])): ?>
                                <p><strong>À partir de </strong> <?= htmlspecialchars($voyage['prix_base']) ?> €</p>
                            <?php endif; ?>

                            <?php
                            $nbEtapes = 0;
                            foreach ($voyage as $cle => $valeur) {
                                if (str_starts_with($cle, 'etape')) $nbEtapes++;
                            }
                            ?>
                            <p><strong>Nombre d’étapes :</strong> <?= $nbEtapes ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    <?php include 'includes/footer.php'; ?>
    <script src="js/panier.js"></script>
</body>
</html>
