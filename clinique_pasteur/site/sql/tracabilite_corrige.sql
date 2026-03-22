-- ============================================================
-- CLINIQUE PASTEUR – Traçabilité
-- VERSION CORRIGÉE – Rayan
-- Exécuter APRÈS crud_parametres.sql
-- Les triggers rendez_vous / reponse_questionnaire sont
-- commentés car ces tables sont créées par Téo (schéma global).
-- À décommenter une fois le schéma complet en place.
-- ============================================================

-- ------------------------------------------------------------
-- 1. TABLE DE LOG
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tracabilite (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    table_modifiee    VARCHAR(100)  NOT NULL,
    action            ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    date_heure        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_enregistrement INT           NULL,
    ancienne_valeur   TEXT          NULL,
    nouvelle_valeur   TEXT          NULL,
    utilisateur_pseudo VARCHAR(50)  NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 2. TRIGGERS – TABLE : questionnaire (existe déjà)
-- ============================================================

DELIMITER $$

CREATE TRIGGER trg_questionnaire_insert
AFTER INSERT ON questionnaire
FOR EACH ROW
BEGIN
    INSERT INTO tracabilite (table_modifiee, action, id_enregistrement, nouvelle_valeur)
    VALUES ('questionnaire', 'INSERT', NEW.id_questionnaire,
        JSON_OBJECT('nom', NEW.nom, 'description', NEW.description));
END$$

CREATE TRIGGER trg_questionnaire_update
AFTER UPDATE ON questionnaire
FOR EACH ROW
BEGIN
    INSERT INTO tracabilite (table_modifiee, action, id_enregistrement, ancienne_valeur, nouvelle_valeur)
    VALUES ('questionnaire', 'UPDATE', NEW.id_questionnaire,
        JSON_OBJECT('nom', OLD.nom, 'description', OLD.description),
        JSON_OBJECT('nom', NEW.nom, 'description', NEW.description));
END$$

CREATE TRIGGER trg_questionnaire_delete
AFTER DELETE ON questionnaire
FOR EACH ROW
BEGIN
    INSERT INTO tracabilite (table_modifiee, action, id_enregistrement, ancienne_valeur)
    VALUES ('questionnaire', 'DELETE', OLD.id_questionnaire,
        JSON_OBJECT('nom', OLD.nom, 'description', OLD.description));
END$$

DELIMITER ;


-- ============================================================
-- 3. TRIGGERS rendez_vous + reponse_questionnaire
--    À décommenter quand Téo aura créé le schéma complet
-- ============================================================

/*

DELIMITER $$

CREATE TRIGGER trg_rdv_insert
AFTER INSERT ON rendez_vous
FOR EACH ROW
BEGIN
    INSERT INTO tracabilite (table_modifiee, action, id_enregistrement, nouvelle_valeur, utilisateur_pseudo)
    VALUES (
        'rendez_vous', 'INSERT', NEW.id_rdv,
        JSON_OBJECT(
            'id_rdv',     NEW.id_rdv,
            'id_patient', NEW.id_patient,
            'id_medecin', NEW.id_medecin,
            'date_rdv',   NEW.date_rdv,
            'statut',     NEW.statut
        ),
        NEW.id_patient
    );
END$$

CREATE TRIGGER trg_rdv_update
AFTER UPDATE ON rendez_vous
FOR EACH ROW
BEGIN
    INSERT INTO tracabilite (table_modifiee, action, id_enregistrement, ancienne_valeur, nouvelle_valeur, utilisateur_pseudo)
    VALUES (
        'rendez_vous', 'UPDATE', NEW.id_rdv,
        JSON_OBJECT('id_patient', OLD.id_patient, 'date_rdv', OLD.date_rdv, 'statut', OLD.statut),
        JSON_OBJECT('id_patient', NEW.id_patient, 'date_rdv', NEW.date_rdv, 'statut', NEW.statut),
        NEW.id_patient
    );
END$$

CREATE TRIGGER trg_rdv_delete
AFTER DELETE ON rendez_vous
FOR EACH ROW
BEGIN
    INSERT INTO tracabilite (table_modifiee, action, id_enregistrement, ancienne_valeur, utilisateur_pseudo)
    VALUES (
        'rendez_vous', 'DELETE', OLD.id_rdv,
        JSON_OBJECT('id_patient', OLD.id_patient, 'date_rdv', OLD.date_rdv, 'statut', OLD.statut),
        OLD.id_patient
    );
END$$

CREATE TRIGGER trg_reponse_insert
AFTER INSERT ON reponse_questionnaire
FOR EACH ROW
BEGIN
    INSERT INTO tracabilite (table_modifiee, action, id_enregistrement, nouvelle_valeur, utilisateur_pseudo)
    VALUES (
        'reponse_questionnaire', 'INSERT', NEW.id_reponse,
        JSON_OBJECT(
            'id_questionnaire', NEW.id_questionnaire,
            'id_patient',       NEW.id_patient,
            'id_question',      NEW.id_question,
            'valeur',           NEW.valeur
        ),
        NEW.id_patient
    );
END$$

CREATE TRIGGER trg_reponse_update
AFTER UPDATE ON reponse_questionnaire
FOR EACH ROW
BEGIN
    INSERT INTO tracabilite (table_modifiee, action, id_enregistrement, ancienne_valeur, nouvelle_valeur, utilisateur_pseudo)
    VALUES (
        'reponse_questionnaire', 'UPDATE', NEW.id_reponse,
        JSON_OBJECT('valeur', OLD.valeur),
        JSON_OBJECT('valeur', NEW.valeur),
        NEW.id_patient
    );
END$$

CREATE TRIGGER trg_reponse_delete
AFTER DELETE ON reponse_questionnaire
FOR EACH ROW
BEGIN
    INSERT INTO tracabilite (table_modifiee, action, id_enregistrement, ancienne_valeur, utilisateur_pseudo)
    VALUES (
        'reponse_questionnaire', 'DELETE', OLD.id_reponse,
        JSON_OBJECT('id_question', OLD.id_question, 'valeur', OLD.valeur),
        OLD.id_patient
    );
END$$

DELIMITER ;

*/