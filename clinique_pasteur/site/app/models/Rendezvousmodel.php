<?php
// models/RendezVousModel.php
// US4 – Consultation des rendez-vous de la semaine (Médecin)
// Clinique Pasteur – Rayan

class RendezVousModel {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * US4 – Récupère tous les RDV de la semaine courante pour un médecin.
     * Retourne aussi le dernier questionnaire rempli par chaque patient.
     */
    public function getRdvSemaineByMedecin(int $idMedecin): array {
        $sql = "
            SELECT
                r.id_rdv,
                r.date_rdv,
                r.statut,
                p.pseudonyme          AS patient_pseudo,
                p.id_patient,
                e.nom                 AS etude_nom,
                -- Date de la dernière réponse questionnaire du patient
                (
                    SELECT MAX(rq.date_saisie)
                    FROM reponse_questionnaire rq
                    WHERE rq.id_patient = p.id_patient
                ) AS derniere_saisie,
                -- Le patient a-t-il rempli son questionnaire ?
                (
                    SELECT COUNT(*)
                    FROM reponse_questionnaire rq
                    INNER JOIN rendez_vous rdv2 ON rdv2.id_patient = rq.id_patient
                    WHERE rq.id_patient = p.id_patient
                      AND rq.date_saisie >= DATE_SUB(r.date_rdv, INTERVAL 7 DAY)
                ) AS questionnaire_rempli
            FROM rendez_vous r
            INNER JOIN patient p ON p.id_patient = r.id_patient
            LEFT JOIN etude e ON e.id_etude = p.id_etude
            WHERE r.id_medecin   = :id_medecin
              AND r.date_rdv    >= DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')
              AND r.date_rdv    <= DATE_FORMAT(
                                      DATE_ADD(NOW(), INTERVAL (6 - WEEKDAY(NOW())) DAY),
                                      '%Y-%m-%d 23:59:59'
                                   )
            ORDER BY r.date_rdv ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_medecin' => $idMedecin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le détail d'un RDV + réponses du questionnaire associé.
     */
    public function getRdvDetail(int $idRdv): array|false {
        $stmt = $this->pdo->prepare("
            SELECT r.*, p.pseudonyme AS patient_pseudo, p.id_patient
            FROM rendez_vous r
            INNER JOIN patient p ON p.id_patient = r.id_patient
            LEFT JOIN etude e ON e.id_etude = p.id_etude
            WHERE r.id_rdv = :id
        ");
        $stmt->execute([':id' => $idRdv]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les réponses du dernier questionnaire rempli par un patient.
     */
    public function getDernieresReponses(int $idPatient): array {
        $stmt = $this->pdo->prepare("
            SELECT
                q.texte   AS question,
                t.libelle AS theme,
                rq.valeur,
                rq.date_saisie,
                q.inversee
            FROM reponse_questionnaire rq
            INNER JOIN question q ON q.id_question = rq.id_question
            INNER JOIN theme t    ON t.id_theme    = q.id_theme
            WHERE rq.id_patient = :id_patient
              AND rq.date_saisie = (
                SELECT MAX(rq2.date_saisie)
                FROM reponse_questionnaire rq2
                WHERE rq2.id_patient = :id_patient2
              )
            ORDER BY t.ordre, q.ordre
        ");
        $stmt->execute([':id_patient' => $idPatient, ':id_patient2' => $idPatient]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * US8 – Récupère les RDV dont la date est dans exactement 7 jours OU demain.
     * Utilisé par le cron de notification.
     */
    public function getRdvPourNotification(int $joursAvant): array {
        $stmt = $this->pdo->prepare("
            SELECT
                r.id_rdv,
                r.date_rdv,
                p.id_patient,
                p.pseudonyme,
                p.email_chiffre      -- déchiffré dans le model si besoin
            FROM rendez_vous r
            INNER JOIN patient p ON p.id_patient = r.id_patient
            WHERE DATE(r.date_rdv) = DATE_ADD(CURDATE(), INTERVAL :jours DAY)
              AND r.statut = 'planifié'
        ");
        $stmt->execute([':jours' => $joursAvant]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Enregistre qu'une notification a été envoyée (évite les doublons).
     */
    public function logNotification(int $idRdv, int $joursAvant): void {
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO notification_log (id_rdv, jours_avant, date_envoi)
            VALUES (:id_rdv, :jours, NOW())
        ");
        $stmt->execute([':id_rdv' => $idRdv, ':jours' => $joursAvant]);
    }

    /**
     * Vérifie si la notification a déjà été envoyée pour ce RDV.
     */
    public function notificationDejaEnvoyee(int $idRdv, int $joursAvant): bool {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM notification_log
            WHERE id_rdv = :id_rdv AND jours_avant = :jours
        ");
        $stmt->execute([':id_rdv' => $idRdv, ':jours' => $joursAvant]);
        return (int)$stmt->fetchColumn() > 0;
    }
}