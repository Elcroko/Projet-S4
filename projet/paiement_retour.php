<?php
session_start();
require_once('getapikey.php');

$transaction = $_GET['transaction'] ?? '';
$montant = $_GET['montant'] ?? '';
$vendeur = $_GET['vendeur'] ?? '';
$statut = $_GET['status'] ?? '';
$control_recu = $_GET['control'] ?? '';

$api_key = getAPIKey($vendeur);
$control_attendu = md5($api_key . "#" . $transaction . "#" . $montant . "#" . $vendeur . "#" . $statut . "#");



if ($control_attendu !== $control_recu) {
    die("Erreur : vérification du retour CYBank impossible.");
}

if ($statut === "accepted") {
    header("Location: paiement_valide.php");
    exit;
} else {
    header("Location: paiement_refuse.php");
    exit;
}
