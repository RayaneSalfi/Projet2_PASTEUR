-- ============================================================
-- CLINIQUE PASTEUR – US8
-- Table pour éviter les doublons de notifications
-- Rayan
-- ============================================================

CREATE TABLE IF NOT EXISTS notification_log (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_rdv      INT NOT NULL,
    jours_avant INT NOT NULL COMMENT '7 = rappel 1 semaine, 1 = rappel veille',
    date_envoi  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut      ENUM('envoyé', 'erreur') DEFAULT 'envoyé',
    UNIQUE KEY unique_notif (id_rdv, jours_avant),
    FOREIGN KEY (id_rdv) REFERENCES rendez_vous(id_rdv) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;