<?php
// controllers/CrudController.php
// Clinique Pasteur – CRUD Tables paramètres

require_once __DIR__ . '/../models/QuestionnaireModel.php';

class CrudController {

    private QuestionnaireModel $model;

    public function __construct(PDO $pdo) {
        $this->model = new QuestionnaireModel($pdo);
    }

    // ── ROUTAGE PRINCIPAL ─────────────────────────────────────

    public function handle(string $entite, string $action, array $params = []): void {
        match ($entite) {
            'etude'          => $this->handleEtude($action, $params),
            'questionnaire'  => $this->handleQuestionnaire($action, $params),
            'theme'          => $this->handleTheme($action, $params),
            'question'       => $this->handleQuestion($action, $params),
            default          => $this->notFound()
        };
    }

    // ── ETUDES ───────────────────────────────────────────────

    private function handleEtude(string $action, array $p): void {
        switch ($action) {
            case 'list':
                $data = $this->model->getAllEtudes();
                require __DIR__ . '/../views/crud/etude_list.php';
                break;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->validateCsrf();
                    $code = htmlspecialchars(trim($_POST['code']));
                    $nom  = htmlspecialchars(trim($_POST['nom']));
                    $desc = htmlspecialchars(trim($_POST['description']));
                    if ($code && $nom) {
                        $this->model->createEtude($code, $nom, $desc);
                        header('Location: index.php?entite=etude&action=list&success=1');
                        exit;
                    }
                }
                require __DIR__ . '/../views/crud/etude_form.php';
                break;

            case 'edit':
                $id   = (int)($p['id'] ?? 0);
                $item = $this->model->getEtudeById($id);
                if (!$item) { $this->notFound(); return; }
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->validateCsrf();
                    $code = htmlspecialchars(trim($_POST['code']));
                    $nom  = htmlspecialchars(trim($_POST['nom']));
                    $desc = htmlspecialchars(trim($_POST['description']));
                    if ($code && $nom) {
                        $this->model->updateEtude($id, $code, $nom, $desc);
                        header('Location: index.php?entite=etude&action=list&success=2');
                        exit;
                    }
                }
                require __DIR__ . '/../views/crud/etude_form.php';
                break;

            case 'delete':
                $id = (int)($p['id'] ?? 0);
                $this->validateCsrf();
                $this->model->deleteEtude($id);
                header('Location: index.php?entite=etude&action=list&success=3');
                exit;
        }
    }

    // ── QUESTIONNAIRES ───────────────────────────────────────

    private function handleQuestionnaire(string $action, array $p): void {
        switch ($action) {
            case 'list':
                $data = $this->model->getAllQuestionnaires();
                require __DIR__ . '/../views/crud/questionnaire_list.php';
                break;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->validateCsrf();
                    $nom  = htmlspecialchars(trim($_POST['nom']));
                    $desc = htmlspecialchars(trim($_POST['description']));
                    if ($nom) {
                        $this->model->createQuestionnaire($nom, $desc);
                        header('Location: index.php?entite=questionnaire&action=list&success=1');
                        exit;
                    }
                }
                require __DIR__ . '/../views/crud/questionnaire_form.php';
                break;

            case 'edit':
                $id   = (int)($p['id'] ?? 0);
                $item = $this->model->getQuestionnaireById($id);
                if (!$item) { $this->notFound(); return; }
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->validateCsrf();
                    $nom  = htmlspecialchars(trim($_POST['nom']));
                    $desc = htmlspecialchars(trim($_POST['description']));
                    if ($nom) {
                        $this->model->updateQuestionnaire($id, $nom, $desc);
                        header('Location: index.php?entite=questionnaire&action=list&success=2');
                        exit;
                    }
                }
                require __DIR__ . '/../views/crud/questionnaire_form.php';
                break;

            case 'delete':
                $id = (int)($p['id'] ?? 0);
                $this->validateCsrf();
                $this->model->deleteQuestionnaire($id);
                header('Location: index.php?entite=questionnaire&action=list&success=3');
                exit;
        }
    }

    // ── THEMES ───────────────────────────────────────────────

    private function handleTheme(string $action, array $p): void {
        $idQ = (int)($p['id_questionnaire'] ?? 0);
        switch ($action) {
            case 'list':
                $questionnaire = $this->model->getQuestionnaireById($idQ);
                $data          = $this->model->getThemesByQuestionnaire($idQ);
                require __DIR__ . '/../views/crud/theme_list.php';
                break;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->validateCsrf();
                    $libelle = htmlspecialchars(trim($_POST['libelle']));
                    $ordre   = (int)($_POST['ordre'] ?? 0);
                    if ($libelle) {
                        $this->model->createTheme($idQ, $libelle, $ordre);
                        header("Location: index.php?entite=theme&action=list&id_questionnaire=$idQ&success=1");
                        exit;
                    }
                }
                require __DIR__ . '/../views/crud/theme_form.php';
                break;

            case 'edit':
                $id   = (int)($p['id'] ?? 0);
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->validateCsrf();
                    $libelle = htmlspecialchars(trim($_POST['libelle']));
                    $ordre   = (int)($_POST['ordre'] ?? 0);
                    $this->model->updateTheme($id, $libelle, $ordre);
                    header("Location: index.php?entite=theme&action=list&id_questionnaire=$idQ&success=2");
                    exit;
                }
                require __DIR__ . '/../views/crud/theme_form.php';
                break;

            case 'delete':
                $id = (int)($p['id'] ?? 0);
                $this->validateCsrf();
                $this->model->deleteTheme($id);
                header("Location: index.php?entite=theme&action=list&id_questionnaire=$idQ&success=3");
                exit;
        }
    }

    // ── QUESTIONS ────────────────────────────────────────────

    private function handleQuestion(string $action, array $p): void {
        $idTheme = (int)($p['id_theme'] ?? 0);
        switch ($action) {
            case 'list':
                $data = $this->model->getQuestionsByTheme($idTheme);
                require __DIR__ . '/../views/crud/question_list.php';
                break;

            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->validateCsrf();
                    $texte   = htmlspecialchars(trim($_POST['texte']));
                    $ordre   = (int)($_POST['ordre'] ?? 0);
                    $inv     = isset($_POST['inversee']);
                    if ($texte) {
                        $this->model->createQuestion($idTheme, $texte, $ordre, $inv);
                        header("Location: index.php?entite=question&action=list&id_theme=$idTheme&success=1");
                        exit;
                    }
                }
                require __DIR__ . '/../views/crud/question_form.php';
                break;

            case 'edit':
                $id = (int)($p['id'] ?? 0);
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->validateCsrf();
                    $texte = htmlspecialchars(trim($_POST['texte']));
                    $ordre = (int)($_POST['ordre'] ?? 0);
                    $inv   = isset($_POST['inversee']);
                    $this->model->updateQuestion($id, $texte, $ordre, $inv);
                    header("Location: index.php?entite=question&action=list&id_theme=$idTheme&success=2");
                    exit;
                }
                require __DIR__ . '/../views/crud/question_form.php';
                break;

            case 'delete':
                $id = (int)($p['id'] ?? 0);
                $this->validateCsrf();
                $this->model->deleteQuestion($id);
                header("Location: index.php?entite=question&action=list&id_theme=$idTheme&success=3");
                exit;
        }
    }

    // ── UTILITAIRES ──────────────────────────────────────────

    private function validateCsrf(): void {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            http_response_code(403);
            die('Token CSRF invalide.');
        }
    }

    private function notFound(): void {
        http_response_code(404);
        echo "<p>Page introuvable.</p>";
    }
}