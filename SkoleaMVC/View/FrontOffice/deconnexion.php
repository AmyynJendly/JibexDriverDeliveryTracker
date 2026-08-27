<?php
require_once __DIR__ . '/../../bootstrap.php';

$token = $_POST['_csrf'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($token)) {
    unset($_SESSION['utilisateur']);
    session_regenerate_id(true);
    flash_set('info', 'Vous avez ete deconnecte.');
}

header('Location: connexion.php');
exit;
