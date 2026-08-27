<?php
// Deplace un fichier televerse vers uploads/{dossier}/, avec
// controle d'extension et de taille.
class Upload
{
    // $fichier est une entree de $_FILES. Retourne le chemin relatif stocke
    // (ex: "cours/64f...jpg"), ou null si aucun fichier n'a ete envoye.
    public static function stocker($fichier, $dossier, $extensionsAutorisees, $tailleMaxOctets)
    {
        if (!isset($fichier['error']) || $fichier['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Le televersement du fichier a echoue.");
        }

        if ($fichier['size'] > $tailleMaxOctets) {
            $maxMo = (int) ($tailleMaxOctets / 1024 / 1024);
            throw new RuntimeException("Le fichier depasse la taille maximale autorisee ({$maxMo} Mo).");
        }

        $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $extensionsAutorisees, true)) {
            throw new RuntimeException('Extension de fichier non autorisee (' . implode(', ', $extensionsAutorisees) . ').');
        }

        if (!is_uploaded_file($fichier['tmp_name'])) {
            throw new RuntimeException('Televersement invalide.');
        }

        $nomFichier = bin2hex(random_bytes(16)) . '.' . $extension;
        $dossierComplet = __DIR__ . '/uploads/' . $dossier;

        if (!is_dir($dossierComplet)) {
            mkdir($dossierComplet, 0755, true);
        }

        if (!move_uploaded_file($fichier['tmp_name'], $dossierComplet . '/' . $nomFichier)) {
            throw new RuntimeException("Impossible d'enregistrer le fichier.");
        }

        return $dossier . '/' . $nomFichier;
    }
}
