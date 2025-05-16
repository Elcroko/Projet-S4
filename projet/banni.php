<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte banni</title>
    <link rel="stylesheet" href="css/banni.css">
</head>
<body>
    <div class="banni-container">
        <div class="icon">🔒</div>
        <h1>Accès refusé</h1>
        <p>Votre compte a été <strong>banni</strong> par un administrateur.<br>Vous ne pouvez plus accéder au site.</p>
        <a href="index.php">Retour à l’accueil</a>
    </div>
</body>
</html>
