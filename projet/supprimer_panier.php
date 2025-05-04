<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_id'])) {
    $id = $_POST['supprimer_id'];

    if (isset($_SESSION['panier'])) {
        $_SESSION['panier'] = array_filter($_SESSION['panier'], function ($voyage) use ($id) {
            return $voyage['id'] !== $id;
        });
        $_SESSION['panier'] = array_values($_SESSION['panier']); // Réindexe
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false]);
