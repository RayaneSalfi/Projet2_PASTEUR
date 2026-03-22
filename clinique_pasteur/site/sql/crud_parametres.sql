-- ============================================================
-- CLINIQUE PASTEUR – Gestion des Questionnaires
-- CRUD – Tables paramètres
-- ============================================================

-- ------------------------------------------------------------
-- TABLES PARAMÈTRES (si pas encore créées)
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS etude (
    id_etude     INT AUTO_INCREMENT PRIMARY KEY,
    code         VARCHAR(50)  NOT NULL UNIQUE,
    nom          VARCHAR(150) NOT NULL,
    description  TEXT,
    actif        TINYINT(1)   DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS questionnaire (
    id_questionnaire INT AUTO_INCREMENT PRIMARY KEY,
    nom              VARCHAR(150) NOT NULL,
    description      TEXT,
    actif            TINYINT(1)   DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS theme (
    id_theme         INT AUTO_INCREMENT PRIMARY KEY,
    id_questionnaire INT NOT NULL,
    libelle          VARCHAR(150) NOT NULL,
    ordre            INT DEFAULT 0,
    FOREIGN KEY (id_questionnaire) REFERENCES questionnaire(id_questionnaire) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS question (
    id_question  INT AUTO_INCREMENT PRIMARY KEY,
    id_theme     INT NOT NULL,
    texte        TEXT NOT NULL,
    echelle_min  INT DEFAULT 1,
    echelle_max  INT DEFAULT 5,
    inversee     TINYINT(1) DEFAULT 0,
    ordre        INT DEFAULT 0,
    FOREIGN KEY (id_theme) REFERENCES theme(id_theme) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS etude_questionnaire (
    id_etude         INT NOT NULL,
    id_questionnaire INT NOT NULL,
    PRIMARY KEY (id_etude, id_questionnaire),
    FOREIGN KEY (id_etude)         REFERENCES etude(id_etude) ON DELETE CASCADE,
    FOREIGN KEY (id_questionnaire) REFERENCES questionnaire(id_questionnaire) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- DONNÉES DE TEST – tables paramètres
-- ============================================================

INSERT INTO etude (code, nom, description) VALUES
('QLQ-C30',  'Qualité de vie cancer',         'Questionnaire standardisé EORTC – 30 items'),
('QLQ-CR29', 'Cancer colorectal',              'Module spécifique cancer colorectal'),
('CANTO',    'Cancer Toxicities',              'Toxicités chroniques liées aux traitements cancer du sein'),
('RECARE',   'Réhabilitation Cancer Rectum',   'Programme de réhabilitation post-chirurgie rectum'),
('QUFORA',   'Evaluation dispositif Qufora',   'Suivi patient utilisant le dispositif Qufora');

INSERT INTO questionnaire (nom, description) VALUES
('QLQ-C30',  'Évaluation de la qualité de vie – 30 questions'),
('CANTO',    'Toxicités chroniques – cancer du sein'),
('Qufora',   'Évaluation patient dispositif Qufora');

INSERT INTO theme (id_questionnaire, libelle, ordre) VALUES
(1, 'Capacités physiques',             1),
(1, 'Activités quotidiennes et rôle',  2),
(1, 'Fonction cognitive',              3),
(1, 'Émotions',                        4),
(1, 'Relations sociales',              5),
(1, 'Sommeil et énergie',              6),
(1, 'Symptômes physiques',             7),
(1, 'Bien-être général',               8);

INSERT INTO question (id_theme, texte, ordre) VALUES
(1, 'J\'ai eu des difficultés à marcher sur de courtes distances.',        1),
(1, 'J\'ai eu du mal à porter des objets ordinaires (sac, courses, etc.).', 2),
(1, 'Je me suis senti(e) limité(e) pour monter des escaliers.',            3),
(2, 'J\'ai eu du mal à accomplir mes tâches professionnelles ou scolaires.', 1),
(2, 'Mes responsabilités domestiques ont été difficiles à gérer.',          2),
(3, 'J\'ai eu du mal à me concentrer.',                                    1),
(3, 'J\'ai eu des difficultés à me souvenir d\'informations récentes.',    2),
(8, 'Globalement, ma qualité de vie cette semaine a été bonne.', 1);

INSERT INTO etude_questionnaire VALUES
(1, 1),
(2, 1),
(3, 2),
(5, 3);