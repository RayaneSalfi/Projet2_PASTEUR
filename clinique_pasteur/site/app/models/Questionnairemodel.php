<?php
// models/QuestionnaireModel.php
// Clinique Pasteur – CRUD Tables paramètres

class QuestionnaireModel {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ── ETUDES ──────────────────────────────────────────────

    public function getAllEtudes(): array {
        $stmt = $this->pdo->query("SELECT * FROM etude ORDER BY nom");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEtudeById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM etude WHERE id_etude = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createEtude(string $code, string $nom, string $description): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO etude (code, nom, description) VALUES (?, ?, ?)"
        );
        $stmt->execute([$code, $nom, $description]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateEtude(int $id, string $code, string $nom, string $description): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE etude SET code = ?, nom = ?, description = ? WHERE id_etude = ?"
        );
        return $stmt->execute([$code, $nom, $description, $id]);
    }

    public function deleteEtude(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM etude WHERE id_etude = ?");
        return $stmt->execute([$id]);
    }

    // ── QUESTIONNAIRES ───────────────────────────────────────

    public function getAllQuestionnaires(): array {
        $stmt = $this->pdo->query("SELECT * FROM questionnaire ORDER BY nom");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestionnaireById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM questionnaire WHERE id_questionnaire = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createQuestionnaire(string $nom, string $description): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO questionnaire (nom, description) VALUES (?, ?)"
        );
        $stmt->execute([$nom, $description]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateQuestionnaire(int $id, string $nom, string $description): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE questionnaire SET nom = ?, description = ? WHERE id_questionnaire = ?"
        );
        return $stmt->execute([$nom, $description, $id]);
    }

    public function deleteQuestionnaire(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM questionnaire WHERE id_questionnaire = ?");
        return $stmt->execute([$id]);
    }

    // ── THEMES ───────────────────────────────────────────────

    public function getThemesByQuestionnaire(int $idQuestionnaire): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM theme WHERE id_questionnaire = ? ORDER BY ordre"
        );
        $stmt->execute([$idQuestionnaire]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTheme(int $idQuestionnaire, string $libelle, int $ordre = 0): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO theme (id_questionnaire, libelle, ordre) VALUES (?, ?, ?)"
        );
        $stmt->execute([$idQuestionnaire, $libelle, $ordre]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateTheme(int $id, string $libelle, int $ordre): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE theme SET libelle = ?, ordre = ? WHERE id_theme = ?"
        );
        return $stmt->execute([$libelle, $ordre, $id]);
    }

    public function deleteTheme(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM theme WHERE id_theme = ?");
        return $stmt->execute([$id]);
    }

    // ── QUESTIONS ────────────────────────────────────────────

    public function getQuestionsByTheme(int $idTheme): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM question WHERE id_theme = ? ORDER BY ordre"
        );
        $stmt->execute([$idTheme]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createQuestion(int $idTheme, string $texte, int $ordre = 0, bool $inversee = false): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO question (id_theme, texte, ordre, inversee) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$idTheme, $texte, $ordre, (int)$inversee]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateQuestion(int $id, string $texte, int $ordre, bool $inversee): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE question SET texte = ?, ordre = ?, inversee = ? WHERE id_question = ?"
        );
        return $stmt->execute([$texte, $ordre, (int)$inversee, $id]);
    }

    public function deleteQuestion(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM question WHERE id_question = ?");
        return $stmt->execute([$id]);
    }
}