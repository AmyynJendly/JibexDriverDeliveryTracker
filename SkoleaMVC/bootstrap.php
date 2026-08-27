<?php
// Fichier commun inclus par chaque page : demarre la session et charge
// la configuration + les fonctions utilitaires.
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Upload.php';
