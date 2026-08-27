<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Gestion minimale des televersements de fichiers (image de cours,
 * documents de ressources), avec controle d'extension et de taille.
 */
final class Upload
{
    /**
     * Deplace un fichier televerse vers public/uploads/{dossier}/ et
     * retourne le chemin relatif stocke (ex: "cours/64f...jpg"), ou null
     * si aucun fichier n'a ete fourni.
     *
     * @param array<string, mixed> $fichier   une entree de $_FILES
     * @param string[]             $extensionsAutorisees
     *
     * @throws \RuntimeException si le fichier est invalide
     */
    public static function stocker(array $fichier, string $dossier, array $extensionsAutorisees, int $tailleMaxOctets): ?string
    {
        if (!isset($fichier['error']) || $fichier['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Le televersement du fichier a echoue.");
        }

        if ($fichier['size'] > $tailleMaxOctets) {
            $maxMo = (int) ($tailleMaxOctets / 1024 / 1024);
            throw new \RuntimeException("Le fichier depasse la taille maximale autorisee ({$maxMo} Mo).");
        }

        $extension = strtolower((string) pathinfo((string) $fichier['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $extensionsAutorisees, true)) {
            throw new \RuntimeException('Extension de fichier non autorisee (' . implode(', ', $extensionsAutorisees) . ').');
        }

        if (!is_uploaded_file($fichier['tmp_name'])) {
            throw new \RuntimeException('Televersement invalide.');
        }

        $nomFichier = bin2hex(random_bytes(16)) . '.' . $extension;
        $dossierComplet = dirname(__DIR__, 2) . '/public/uploads/' . $dossier;

        if (!is_dir($dossierComplet)) {
            mkdir($dossierComplet, 0755, true);
        }

        if (!move_uploaded_file($fichier['tmp_name'], $dossierComplet . '/' . $nomFichier)) {
            throw new \RuntimeException("Impossible d'enregistrer le fichier.");
        }

        return $dossier . '/' . $nomFichier;
    }
}
