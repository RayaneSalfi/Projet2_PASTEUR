<?php
// app/controllers/AuthController.php
// Clinique Pasteur – Authentification

class AuthController {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function handle(string $action, array $params = []): void {
        match ($action) {
            'login'  => $this->login(),
            'verify' => $this->verify2FA(),
            'logout' => $this->logout(),
            default  => $this->login()
        };
    }

    // ── ÉTAPE 1 : LOGIN ───────────────────────────────────────

    private function login(): void {
        if (!empty($_SESSION['user_id'])) {
            $this->redirectByRole($_SESSION['role']);
            return;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (empty($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
            }
            $_SESSION['login_attempts']++;

            if ($_SESSION['login_attempts'] > 10) {
                $error = 'Trop de tentatives. Videz vos cookies et réessayez.';
                require __DIR__ . '/../views/auth/login.php';
                return;
            }

            $email = trim($_POST['email'] ?? '');
            $mdp   = $_POST['password'] ?? '';
            $user  = $this->findUser($email);

            if ($user && password_verify($mdp, $user['mot_de_passe'])) {

                $code = strtoupper(bin2hex(random_bytes(3)));
                $_SESSION['2fa_code']    = $code;
                $_SESSION['2fa_user']    = $user;
                $_SESSION['2fa_expires'] = time() + 300;
                $_SESSION['2fa_debug']   = $code;
                $_SESSION['login_attempts'] = 0;

                require __DIR__ . '/../views/auth/twofa.php';
                return;

            } else {
                $error = 'Email ou mot de passe incorrect.';
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    // ── ÉTAPE 2 : 2FA ─────────────────────────────────────────

    private function verify2FA(): void {
        if (empty($_SESSION['2fa_code'])) {
            header('Location: index.php?ctrl=auth&action=login');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = strtoupper(trim($_POST['code'] ?? ''));

            if (time() > ($_SESSION['2fa_expires'] ?? 0)) {
                $error = 'Code expiré. Reconnectez-vous.';
            } elseif ($code === $_SESSION['2fa_code']) {

                $user = $_SESSION['2fa_user'];
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['role']       = $user['role'];
                $_SESSION['nom']        = $user['nom'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                unset($_SESSION['2fa_code'], $_SESSION['2fa_user'],
                      $_SESSION['2fa_expires'], $_SESSION['2fa_debug']);

                $this->redirectByRole($user['role']);
                return;

            } else {
                $error = 'Code incorrect.';
            }
        }

        require __DIR__ . '/../views/auth/twofa.php';
    }

    // ── DÉCONNEXION ───────────────────────────────────────────

    private function logout(): void {
        session_destroy();
        header('Location: index.php?ctrl=auth&action=login');
        exit;
    }

    // ── TROUVER UTILISATEUR ───────────────────────────────────

    private function findUser(string $email): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT id_medecin AS id, nom, mot_de_passe, 'medecin' AS role FROM medecin WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) return $user;

        $stmt = $this->pdo->prepare(
            "SELECT id_patient AS id, pseudonyme AS nom, mot_de_passe, 'patient' AS role FROM patient WHERE email_hash = ?"
        );
        $stmt->execute([hash('sha256', $email)]);
        return $stmt->fetch() ?: false;
    }

    // ── REDIRECTION ───────────────────────────────────────────

    private function redirectByRole(string $role): void {
        match ($role) {
            'medecin' => header('Location: index.php?ctrl=rdv&action=semaine'),
            'patient' => header('Location: index.php?ctrl=rdv&action=semaine'),
            'admin'   => header('Location: index.php?ctrl=crud&action=list&entite=questionnaire'),
            default   => header('Location: index.php?ctrl=auth&action=login'),
        };
        exit;
    }
}