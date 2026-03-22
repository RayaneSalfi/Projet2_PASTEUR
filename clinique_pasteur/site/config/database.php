<?php
// config/database.php
// Clinique Pasteur – Configuration de la base de données

define('DB_HOST', 'localhost'); // Adresse IP du conteneur MySQL (à adapter si besoin)
define('DB_NAME', 'clinique_pasteur');
define('DB_USER', 'root');
define('DB_PASS', ''); // ⚠️ mets ton mot de passe Workbench ici si tu en as un

// Connexion PDO unique (singleton)
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die('❌ Connexion BDD échouée : ' . $e->getMessage());
        }
    }
    return $pdo;
}