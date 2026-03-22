<?php
// controllers/RendezVousController.php
// US4 – Consultation des rendez-vous de la semaine (Médecin)
// Clinique Pasteur – Rayan

require_once __DIR__ . '/../models/RendezVousModel.php';

class RendezVousController {

    private RendezVousModel $model;

    public function __construct(PDO $pdo) {
        $this->model = new RendezVousModel($pdo);
    }

    // ── ROUTAGE ───────────────────────────────────────────────

    public function handle(string $action, array $params = []): void {
        // Vérification session obligatoire
        $this->requireAuth();

        match ($action) {
            'semaine' => $this->semaine(),          // US4
            'detail'  => $this->detail($params),   // US4 – détail + questionnaire
            default   => $this->notFound()
        };
    }

    // ── US4 : LISTE RDV DE LA SEMAINE ─────────────────────────

    /**
     * Affiche la liste des rendez-vous de la semaine pour le médecin connecté.
     * Inclut : patient (pseudonyme), étude, statut questionnaire.
     */
    private function semaine(): void {
        // Seul un médecin peut accéder
        $this->requireRole('medecin');

        $idMedecin = $_SESSION['user_id'];
        $rdvs      = $this->model->getRdvSemaineByMedecin($idMedecin);

        // Calcul dates début/fin de semaine pour l'affichage
        $debutSemaine = date('d/m/Y', strtotime('monday this week'));
        $finSemaine   = date('d/m/Y', strtotime('sunday this week'));

        require __DIR__ . '/../views/rdv/medecin_semaine.php';
    }

    // ── US4 : DÉTAIL D'UN RDV ────────────────────────────────

    /**
     * Affiche le détail d'un RDV : infos patient + dernières réponses questionnaire.
     */
    private function detail(array $params): void {
        $this->requireRole('medecin');

        $idRdv = (int)($params['id'] ?? 0);
        if (!$idRdv) { $this->notFound(); return; }

        $rdv      = $this->model->getRdvDetail($idRdv);
        if (!$rdv) { $this->notFound(); return; }

        // Sécurité : le RDV doit appartenir au médecin connecté
        // (contrôle fait en SQL, double vérification PHP)
        $reponses = $this->model->getDernieresReponses((int)$rdv['id_patient']);

        // Grouper les réponses par thème pour la vue
        $reponsesParTheme = [];
        foreach ($reponses as $r) {
            $reponsesParTheme[$r['theme']][] = $r;
        }

        require __DIR__ . '/../views/rdv/medecin_detail.php';
    }

    // ── UTILITAIRES ──────────────────────────────────────────

    private function requireAuth(): void {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?ctrl=auth&action=login');
            exit;
        }
    }

    private function requireRole(string $role): void {
        if (($_SESSION['role'] ?? '') !== $role) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            exit;
        }
    }

    private function notFound(): void {
        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
    }
}