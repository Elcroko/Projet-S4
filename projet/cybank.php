<?php
session_start();
require_once('getapikey.php'); 

if (!isset($_SESSION['recapitulatif'])) {
    header('Location: index.php');
    exit;
}

$voyage = $_SESSION['recapitulatif'];
$transaction = uniqid();
$montant = number_format($_SESSION['cybank_montant'] , 2, '.', '');
$vendeur = "MEF-2_C"; 
$retour = "http://127.0.0.1:8000/www/Projet-S4-main/projet/paiement_retour.php"; // URL de retour après le paiement


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
