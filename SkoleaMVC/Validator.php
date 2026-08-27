<?php
// Validation cote serveur des formulaires (verifiee en plus du JS,
// au cas ou JavaScript serait desactive).
class Validator
{
    private $data;
    private $errors = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    private function value($field)
    {
        return trim((string) ($this->data[$field] ?? ''));
    }

    public function required($field, $label)
    {
        if ($this->value($field) === '') {
            $this->errors[$field] = "Le champ « {$label} » est obligatoire.";
        }

        return $this;
    }

    public function email($field)
    {
        $value = $this->value($field);
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Merci de saisir une adresse email valide.';
        }

        return $this;
    }

    public function min($field, $length, $label = null)
    {
        $value = $this->value($field);
        if ($value !== '' && mb_strlen($value) < $length) {
            if ($label === null) $label = $field;
            $this->errors[$field] = "« {$label} » doit contenir au moins {$length} caracteres.";
        }

        return $this;
    }

    public function max($field, $length, $label = null)
    {
        $value = $this->value($field);
        if ($value !== '' && mb_strlen($value) > $length) {
            if ($label === null) $label = $field;
            $this->errors[$field] = "« {$label} » ne doit pas depasser {$length} caracteres.";
        }

        return $this;
    }

    // Uniquement des lettres et des espaces (ex: nom, prenom).
    public function alpha($field, $label)
    {
        $value = $this->value($field);
        if ($value !== '' && !preg_match('/^[a-zA-ZÀ-ÿ \-]+$/u', $value)) {
            $this->errors[$field] = "« {$label} » ne doit contenir que des lettres et des espaces.";
        }

        return $this;
    }

    public function numeric($field, $label)
    {
        $value = $this->value($field);
        if ($value !== '' && !is_numeric($value)) {
            $this->errors[$field] = "« {$label} » doit etre une valeur numerique.";
        }

        return $this;
    }

    public function in($field, $allowed, $label)
    {
        $value = $this->value($field);
        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field] = "« {$label} » contient une valeur non autorisee.";
        }

        return $this;
    }

    public function matches($field, $other, $label)
    {
        if ($this->value($field) !== $this->value($other)) {
            $this->errors[$field] = "« {$label} » ne correspond pas a la confirmation.";
        }

        return $this;
    }

    // Pour ajouter une erreur "manuelle" (ex: email deja utilise en base).
    public function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }

        return $this;
    }

    public function fails()
    {
        return $this->errors !== [];
    }

    public function errors()
    {
        return $this->errors;
    }
}
