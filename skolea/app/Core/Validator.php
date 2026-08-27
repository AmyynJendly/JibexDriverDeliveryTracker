<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Validation des donnees cote serveur (obligatoire meme quand une
 * validation JS existe deja cote client, le formulaire pouvant etre
 * soumis sans JavaScript ou avec des donnees falsifiees).
 */
final class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    private function value(string $field): string
    {
        return trim((string) ($this->data[$field] ?? ''));
    }

    public function required(string $field, string $label): self
    {
        if ($this->value($field) === '') {
            $this->errors[$field] = "Le champ « {$label} » est obligatoire.";
        }

        return $this;
    }

    public function email(string $field): self
    {
        $value = $this->value($field);
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Merci de saisir une adresse email valide.';
        }

        return $this;
    }

    public function min(string $field, int $length, ?string $label = null): self
    {
        $value = $this->value($field);
        if ($value !== '' && mb_strlen($value) < $length) {
            $label ??= $field;
            $this->errors[$field] = "« {$label} » doit contenir au moins {$length} caracteres.";
        }

        return $this;
    }

    public function max(string $field, int $length, ?string $label = null): self
    {
        $value = $this->value($field);
        if ($value !== '' && mb_strlen($value) > $length) {
            $label ??= $field;
            $this->errors[$field] = "« {$label} » ne doit pas depasser {$length} caracteres.";
        }

        return $this;
    }

    public function numeric(string $field, string $label): self
    {
        $value = $this->value($field);
        if ($value !== '' && !is_numeric($value)) {
            $this->errors[$field] = "« {$label} » doit etre une valeur numerique.";
        }

        return $this;
    }

    public function in(string $field, array $allowed, string $label): self
    {
        $value = $this->value($field);
        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field] = "« {$label} » contient une valeur non autorisee.";
        }

        return $this;
    }

    public function matches(string $field, string $other, string $label): self
    {
        if ($this->value($field) !== $this->value($other)) {
            $this->errors[$field] = "« {$label} » ne correspond pas a la confirmation.";
        }

        return $this;
    }

    /**
     * Permet d'ajouter une erreur issue d'une regle metier externe
     * (ex : email deja utilise, verifie via une requete PDO).
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field] ??= $message;

        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
