<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$panier = $_SESSION['panier'] ?? [];
$voyagesNonPayes = array_filter($panier, fn($v) => !$v['paiement']);
$nbVoyages = count($voyagesNonPayes);

// Suppression d'un voyage du panier si demandé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_id'])) {
    $idASupprimer = $_POST['supprimer_id'];
    if (isset($_SESSION['panier'])) {
        $_SESSION['panier'] = array_filter($_SESSION['panier'], function($voyage) use ($idASupprimer) {
            return $voyage['id'] !== $idASupprimer;
        });
        $_SESSION['panier'] = array_values($_SESSION['panier']); // Réindexer le tableau
    }
}

?>

<div class="header-panier">
    <div class="icone-panier">
        🧳
        <?php if ($nbVoyages > 0): ?>
            <span class="badge"><?= $nbVoyages ?></span>
        <?php endif; ?>
    </div>
    <div class="dropdown-panier">
        <h3>🧳 Mon Panier</h3>
        <?php if ($nbVoyages === 0): ?>
            <p>Aucun voyage personnalisé non payé.</p>
        <?php else: ?>
            <?php foreach ($voyagesNonPayes as $voyage): ?>
                <div class="item-panier">
                    <img src="<?= htmlspecialchars($voyage['image']) ?>" alt="Image">
                    <div>
                        <p><strong><?= htmlspecialchars($voyage['titre']) ?></strong></p>
                        <a href="cybank.php?id=<?= urlencode($voyage['id']) ?>" class="cart-link">Procéder au paiement</a>
                        <!-- 🧹 Bouton de suppression avec confirmation -->
                        <form method="post" action="panier.php" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce voyage du panier ?');" class="form-suppression">
                            <input type="hidden" name="supprimer_id" value="<?= htmlspecialchars($voyage['id']) ?>">
                            <button type="submit" class="supprimer-btn" title="Supprimer">❌</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
