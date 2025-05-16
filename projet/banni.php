<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte banni</title>
    <link rel="stylesheet" href="connexion.css">
</head>
<body>
    <div class="erreur-connexion" style="margin: 100px auto; max-width: 600px;">
        ❌ Vous avez été banni. Accès refusé.
    </div>
</body>
</html>
