<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}
require_once 'verif_banni.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_id'])) {
    $id = $_POST['supprimer_id'];

    if (isset($_SESSION['panier']) && is_array($_SESSION['panier'])) {
        $_SESSION['panier'] = array_filter($_SESSION['panier'], function ($voyage) use ($id) {
            return !(
                (isset($voyage['_uid']) && $voyage['_uid'] === $id) ||
                (isset($voyage['id']) && $voyage['id'] === $id)
            );
        });

        $_SESSION['panier'] = array_values($_SESSION['panier']);
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false]);
exit;