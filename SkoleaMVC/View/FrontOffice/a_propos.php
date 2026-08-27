<?php
require_once __DIR__ . '/../../bootstrap.php';

$pageTitle = 'A propos';
require __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width:760px;">
        <span class="hero-eyebrow">A propos</span>
        <h1>Une plateforme pensee pour organiser l'apprentissage en ligne</h1>
        <p class="text-muted">
            Skolea a ete concue dans le cadre du module "Projet Technologies Web" pour
            repondre a un besoin simple : donner a chaque acteur d'une formation en ligne
            (administrateur, formateur, etudiant) un espace adapte a son role.
        </p>

        <div class="grid grid-cols-3" style="margin-top:40px;">
            <div class="card card-body">
                <h3>Administrateur</h3>
                <p class="text-muted">Gere les comptes utilisateurs, les categories de cours et consulte les statistiques d'ensemble.</p>
            </div>
            <div class="card card-body">
                <h3>Formateur</h3>
                <p class="text-muted">Cree et organise ses cours en modules, y ajoute des ressources et suit ses propres statistiques.</p>
            </div>
            <div class="card card-body">
                <h3>Etudiant</h3>
                <p class="text-muted">Recherche des cours par categorie ou niveau, s'inscrit et suit sa progression module par module.</p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
