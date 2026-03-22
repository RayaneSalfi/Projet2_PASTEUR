<?php
// app/controllers/NotificationController.php
// Clinique Pasteur – US8 (rappel patient) + US9 (notif médecin)
// Rayan / Thomas

require_once __DIR__ . '/../models/RendezVousModel.php';

class NotificationController {

    private PDO $pdo;
    private RendezVousModel $model;

    public function __construct(PDO $pdo) {
        $this->pdo   = $pdo;
        $this->model = new RendezVousModel($pdo);
    }

    // ── ROUTAGE ───────────────────────────────────────────────

    public function handle(string $action, array $params = []): void {
        match ($action) {
            'send_rappels'   => $this->sendRappels(),   // US8 – cron J-7 et J-1
            'notify_medecin' => $this->notifyMedecin($params), // US9 – questionnaire rempli
            default          => $this->notFound()
        };
    }

    // ── US8 : RAPPELS AUTOMATIQUES PATIENT ───────────────────

    /**
     * Appelé par le cron tous les matins à 08h00.
     * Envoie un rappel aux patients dont le RDV est dans 7 jours ou demain.
     */
    private function sendRappels(): void {
        $envoyes = 0;

        foreach ([7, 1] as $joursAvant) {
            $rdvs = $this->model->getRdvPourNotification($joursAvant);

            foreach ($rdvs as $rdv) {
                // Vérifier anti-doublon
                if ($this->model->notificationDejaEnvoyee((int)$rdv['id_rdv'], $joursAvant)) {
                    continue;
                }

                // Déchiffrer l'email du patient
                $email = $this->dechiffrerEmail($rdv['email_chiffre'] ?? '');
                if (!$email) continue;

                $sujet = $joursAvant === 7
                    ? 'Rappel : questionnaire à remplir – RDV dans 1 semaine'
                    : 'Rappel : questionnaire à remplir – RDV demain';

                $corps = $this->buildEmailRappel(
                    $rdv['pseudonyme'],
                    date('d/m/Y à H:i', strtotime($rdv['date_rdv'])),
                    $joursAvant
                );

                $ok = $this->sendMail($email, $sujet, $corps);

                if ($ok) {
                    $this->model->logNotification((int)$rdv['id_rdv'], $joursAvant);
                    $envoyes++;
                }
            }
        }

        echo "US8 : {$envoyes} rappel(s) envoyé(s).";
    }

    // ── US9 : NOTIFICATION MÉDECIN ───────────────────────────

    /**
     * Appelé automatiquement quand un patient soumet son questionnaire.
     * Notifie le médecin par email.
     */
    private function notifyMedecin(array $params): void {
        $idPatient = (int)($params['id_patient'] ?? 0);
        if (!$idPatient) return;

        // Récupérer le médecin du patient
        $stmt = $this->pdo->prepare("
            SELECT m.email, m.nom, p.pseudonyme
            FROM patient p
            INNER JOIN medecin m ON m.id_medecin = p.id_medecin
            WHERE p.id_patient = ?
        ");
        $stmt->execute([$idPatient]);
        $data = $stmt->fetch();
        if (!$data) return;

        $sujet = "Questionnaire rempli – Patient {$data['pseudonyme']}";
        $corps = $this->buildEmailMedecin($data['pseudonyme']);

        $this->sendMail($data['email'], $sujet, $corps);
    }

    // ── CONSTRUCTION EMAILS ───────────────────────────────────

    private function buildEmailRappel(string $pseudo, string $dateRdv, int $jours): string {
        $delai = $jours === 7 ? 'dans <strong>1 semaine</strong>' : '<strong>demain</strong>';
        $lien  = 'https://clinique-pasteur.fr/index.php?ctrl=questionnaire&action=fill';
        return "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'></head><body style='font-family:Arial,sans-serif;background:#f4f4f4;padding:20px'>
          <div style='max-width:560px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1)'>
            <div style='background:#8B1A1A;padding:20px 28px'>
              <h1 style='color:#fff;font-size:18px;margin:0'>Clinique Pasteur</h1>
              <p style='color:#ffcccc;font-size:12px;margin:4px 0 0'>Rappel – Questionnaire patient</p>
            </div>
            <div style='padding:28px'>
              <p style='font-size:14px;line-height:1.7'>Bonjour,</p>
              <p style='font-size:14px;line-height:1.7'>Votre prochain rendez-vous est prévu {$delai}, le <strong>{$dateRdv}</strong>.<br>
              Merci de <strong>compléter votre questionnaire</strong> avant cette date.</p>
              <div style='text-align:center;margin:24px 0'>
                <a href='{$lien}' style='background:#8B1A1A;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:700;font-size:14px'>
                  Remplir mon questionnaire
                </a>
              </div>
              <p style='font-size:12px;color:#999'>Si vous avez déjà rempli votre questionnaire, ignorez ce message.</p>
            </div>
            <div style='background:#f9f9f9;padding:12px 28px;font-size:11px;color:#bbb;border-top:1px solid #eee'>
              Clinique Pasteur · noreply@clinique-pasteur.fr
            </div>
          </div>
        </body></html>";
    }

    private function buildEmailMedecin(string $pseudo): string {
        $lien = 'https://clinique-pasteur.fr/index.php?ctrl=rdv&action=semaine';
        return "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'></head><body style='font-family:Arial,sans-serif;background:#f4f4f4;padding:20px'>
          <div style='max-width:560px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1)'>
            <div style='background:#8B1A1A;padding:20px 28px'>
              <h1 style='color:#fff;font-size:18px;margin:0'>Clinique Pasteur</h1>
              <p style='color:#ffcccc;font-size:12px;margin:4px 0 0'>Nouveau questionnaire disponible</p>
            </div>
            <div style='padding:28px'>
              <p style='font-size:14px;line-height:1.7'>Bonjour Docteur,</p>
              <p style='font-size:14px;line-height:1.7'>Le patient <strong>{$pseudo}</strong> vient de remplir son questionnaire.<br>
              Vous pouvez dès maintenant consulter ses réponses.</p>
              <div style='text-align:center;margin:24px 0'>
                <a href='{$lien}' style='background:#8B1A1A;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:700;font-size:14px'>
                  Voir les réponses
                </a>
              </div>
            </div>
            <div style='background:#f9f9f9;padding:12px 28px;font-size:11px;color:#bbb;border-top:1px solid #eee'>
              Clinique Pasteur · noreply@clinique-pasteur.fr
            </div>
          </div>
        </body></html>";
    }

    // ── UTILITAIRES ───────────────────────────────────────────

    private function sendMail(string $to, string $sujet, string $corps): bool {
        $headers  = "From: Clinique Pasteur <noreply@clinique-pasteur.fr>\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        return mail($to, '=?UTF-8?B?' . base64_encode($sujet) . '?=', $corps, $headers);
    }

    private function dechiffrerEmail(string $emailChiffre): string|false {
        $cle = getenv('APP_CRYPTO_KEY');
        if (!$cle || !$emailChiffre) return false;
        $donnees = base64_decode($emailChiffre);
        $iv      = substr($donnees, 0, 16);
        $chiffre = substr($donnees, 16);
        return openssl_decrypt($chiffre, 'AES-256-CBC', $cle, 0, $iv) ?: false;
    }

    private function notFound(): void {
        http_response_code(404);
        echo "<p>Action introuvable.</p>";
    }
}