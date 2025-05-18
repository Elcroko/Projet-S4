<?php
$page_title = "Paiement refusé - Tempus Odyssey";
$css_file = "paiement.css";
require_once 'verif_banni.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php include 'includes/head.php'; ?>

<body>
<?php include 'includes/header.php'; ?>

    <main>
        <div class="container">
            <h1>Paiement refusé</h1>
            <p>Votre paiement a échoué. Cela peut être dû à un numéro incorrect ou à un manque de fonds.</p>
            <div class="retour-voyage">
                <a href="cybank.php" class="retour-btn">Réessayer le paiement</a>
                <a href="recapitulatif.php" class="retour-btn">Modifier le voyage</a>
            </div>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
