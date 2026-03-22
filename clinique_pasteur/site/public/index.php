<?php
// public/index.php
// Clinique Pasteur – Front Controller (point d'entrée unique)

session_start();

// ── CHARGEMENT CONFIG ─────────────────────────────────────────
require_once __DIR__ . '/../config/database.php';

// ── CHARGEMENT CONTROLLERS ────────────────────────────────────
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/RendezVousController.php';
require_once __DIR__ . '/../app/controllers/CrudController.php';
require_once __DIR__ . '/../app/controllers/NotificationController.php';

// ── ROUTAGE ───────────────────────────────────────────────────
// URL : index.php?ctrl=rdv&action=semaine
$ctrl   = $_GET['ctrl']   ?? 'auth';
$action = $_GET['action'] ?? 'login';
$params = $_GET;

$pdo = getDB();

switch ($ctrl) {

    case 'auth':
        $controller = new AuthController($pdo);
        $controller->handle($action, $params);
        break;

    case 'rdv':
        $controller = new RendezVousController($pdo);
        $controller->handle($action, $params);
        break;

    case 'crud':
        $controller = new CrudController($pdo);
        $controller->handle($_GET['entite'] ?? 'questionnaire', $action, $params);
        break;

    case 'notification':
        $controller = new NotificationController($pdo);
        $controller->handle($action, $params);
        break;

    default:
        http_response_code(404);
        echo "<h2>404 – Page introuvable</h2>";
        break;
}