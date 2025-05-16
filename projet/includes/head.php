<?php
// Titre par défaut si non défini
$page_title = $page_title ?? "Tempus Odyssey";

// Détection automatique du fichier CSS selon la page
if (!isset($css_file)) {
    $current_page = basename($_SERVER['SCRIPT_NAME'], '.php');
    $css_candidate = "css/$current_page.css";
    $css_file = file_exists($css_candidate) ? "$current_page.css" : "styles.css";
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="css/<?= htmlspecialchars($css_file) ?>">
    <link rel="stylesheet" href="css/panier.css">
    <link rel="icon" type="image/png" href="images/portail.png">
</head>
