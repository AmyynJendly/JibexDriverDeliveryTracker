<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <?php include __DIR__ . '/brand.php'; ?>
                <p class="text-muted" style="margin-top:14px;max-width:34ch;">
                    Skolea accompagne etudiants et formateurs dans la creation et le suivi
                    de parcours de formation en ligne, du premier module jusqu'a la reussite.
                </p>
            </div>
            <div>
                <h5>Navigation</h5>
                <a href="<?= e(url('/')) ?>">Accueil</a>
                <a href="<?= e(url('/cours')) ?>">Catalogue des cours</a>
                <a href="<?= e(url('/a-propos')) ?>">A propos</a>
            </div>
            <div>
                <h5>Compte</h5>
                <a href="<?= e(url('/connexion')) ?>">Connexion</a>
                <a href="<?= e(url('/inscription')) ?>">Creer un compte</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> Skolea. Projet academique - Technologies Web 2A.</span>
            <span>Concu et developpe pour un usage pedagogique.</span>
        </div>
    </div>
</footer>
