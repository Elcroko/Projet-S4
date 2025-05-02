<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="panier-wrapper">
    <div class="panier-toggle" title="Voir mon panier">🧳</div>
    <div class="panier-contenu">
        <strong>🧳 Mon Panier</strong><br>
        <?php if (!empty($_SESSION['panier'])): ?>
            <?php foreach ($_SESSION['panier'] as $voyage): ?>
                <div class="voyage-item">
                    <?= htmlspecialchars($voyage['titre']) ?> — <?= htmlspecialchars($voyage['prix']) ?> €
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun voyage personnalisé non payé.</p>
        <?php endif; ?>
        <div class="fermer-panier" onclick="this.parentElement.style.display='none'">✖</div>
    </div>
</div>
