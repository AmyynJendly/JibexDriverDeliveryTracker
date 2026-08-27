<?php

declare(strict_types=1);

namespace App\Controllers\Etudiant;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Paginator;
use App\Models\Inscription;
use App\Models\Module;
use App\Models\Ressource;

final class InscriptionController extends Controller
{
    private const PAR_PAGE = 6;

    public function index(): void
    {
        $this->requireRole('etudiant');

        $model = new Inscription();
        $etudiantId = (int) Auth::id();

        $total = $model->countByEtudiant($etudiantId);
        $paginator = Paginator::fromRequest($total, self::PAR_PAGE);
        $inscriptions = $model->paginateByEtudiant($etudiantId, $paginator->parPage, $paginator->offset);

        $this->view('etudiant/mes-cours', [
            'title' => 'Mes cours',
            'inscriptions' => $inscriptions,
            'paginator' => $paginator,
        ]);
    }

    public function show(string $coursId): void
    {
        $this->requireRole('etudiant');

        $inscription = $this->recupererInscriptionOuBloquer((int) $coursId);
        $modules = (new Module())->byCours((int) $coursId);

        $ressourceModel = new Ressource();
        $ressourcesParModule = [];
        foreach ($modules as $module) {
            $ressourcesParModule[$module['id']] = $ressourceModel->byModule((int) $module['id']);
        }

        $termines = $inscription['modules_termines'] !== null && $inscription['modules_termines'] !== ''
            ? array_map('intval', explode(',', $inscription['modules_termines']))
            : [];

        $this->view('etudiant/suivre-cours', [
            'title' => $inscription['cours_titre'],
            'inscription' => $inscription,
            'modules' => $modules,
            'ressourcesParModule' => $ressourcesParModule,
            'modulesTermines' => $termines,
        ]);
    }

    public function toggleModule(string $coursId, string $moduleId): void
    {
        $this->requireRole('etudiant');
        $this->verifyCsrf();

        $inscription = $this->recupererInscriptionOuBloquer((int) $coursId);
        $totalModules = (new Module())->countByCours((int) $coursId);

        (new Inscription())->basculerModule($inscription, (int) $moduleId, $totalModules);

        $this->redirect('/mes-cours/' . $coursId);
    }

    public function desinscrire(string $coursId): void
    {
        $this->requireRole('etudiant');
        $this->verifyCsrf();

        $inscription = $this->recupererInscriptionOuBloquer((int) $coursId);
        (new Inscription())->delete((int) $inscription['id']);

        flash_set('info', 'Vous vous etes desinscrit de ce cours.');
        $this->redirect('/mes-cours');
    }

    private function recupererInscriptionOuBloquer(int $coursId): array
    {
        $inscription = (new Inscription())->trouverDetailleeParEtudiantEtCours((int) Auth::id(), $coursId);

        if (!$inscription) {
            flash_set('erreur', "Vous n'etes pas inscrit a ce cours.");
            $this->redirect('/mes-cours');
        }

        return $inscription;
    }
}
