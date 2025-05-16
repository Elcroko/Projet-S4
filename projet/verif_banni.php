<?php
session_start();

// Vérifie qu'un utilisateur est connecté
if (isset($_SESSION['user'])) {
    $emailSession = $_SESSION['user']['email'];
    $file = 'json/utilisateurs.json'; // adapte le chemin si nécessaire

    if (file_exists($file)) {
        $utilisateurs = json_decode(file_get_contents($file), true);

        foreach ($utilisateurs as $user) {
            if ($user['email'] === $emailSession) {
                // Si l'utilisateur est banni et n'est pas admin, redirection immédiate
                if (!empty($user['banni']) && empty($user['admin'])) {
                    session_destroy();
                    header('Location: banni.php');
                    exit;
                }
                break;
            }
        }
    }
}
