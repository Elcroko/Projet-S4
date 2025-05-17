<?php
require_once 'verif_banni.php';
require_once('getapikey.php'); 

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Rechercher le voyage personnalisé correspondant dans le panier
    foreach ($_SESSION['panier'] ?? [] as $voyage) {
        if (isset($voyage['id']) && $voyage['id'] === $id) {
            $_SESSION['recapitulatif'] = $voyage;
            $_SESSION['recapitulatif_id'] = $id;
            break;
        }
    }
}


if (!isset($_SESSION['recapitulatif'])) {
    header('Location: index.php');
    exit;
}

$voyage = $_SESSION['recapitulatif'];
$transaction = uniqid();
$montant = number_format($_SESSION['cybank_montant'] , 2, '.', '');
$vendeur = "MEF-2_C"; 
$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$retour = "http://$host$uri/paiement_retour.php";
 // URL de retour après le paiement


$api_key = getAPIKey($vendeur);
$control = md5($api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $retour . "#");

// Stockage temporaire pour vérifier le retour
$_SESSION['cybank_transaction'] = $transaction;
$_SESSION['cybank_montant'] = $montant;
$_SESSION['cybank_vendeur'] = $vendeur;
$_SESSION['cybank_control'] = $control;
$_SESSION['cybank_retour'] = $retour;
?>

<form id="cybankForm" action="https://www.plateforme-smc.fr/cybank/index.php" method="POST">
    <input type="hidden" name="transaction" value="<?= $transaction ?>">
    <input type="hidden" name="montant" value="<?= $montant ?>">
    <input type="hidden" name="vendeur" value="<?= $vendeur ?>">
    <input type="hidden" name="retour" value="<?= $retour ?>">
    <input type="hidden" name="control" value="<?= $control ?>">
    <noscript><input type="submit" value="Payer via CYBank"></noscript>
</form>

<script>document.getElementById('cybankForm').submit();</script>
