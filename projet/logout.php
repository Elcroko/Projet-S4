<?php
session_start();        // On démarre la session
session_unset();        // Supprime toutes les variables de session
session_destroy();      // Détruit la session

// Redirection vers la page d'accueil
header("Location: index.php");
exit;
?>
