<?php
// views/crud/questionnaire_form.php
// Clinique Pasteur – Créer / Modifier un questionnaire
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Questionnaire – Clinique Pasteur</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; color: #222; margin: 0; }
    .topbar {
      background: #8B1A1A; color: #fff; padding: 14px 32px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .topbar h1 { font-size: 18px; margin: 0; }
    .topbar a { color: #fff; font-size: 13px; }
    .container { max-width: 600px; margin: 32px auto; padding: 0 20px; }

    .card {
      background: #fff; border-radius: 8px; padding: 28px 32px;
      box-shadow: 0 1px 4px rgba(0,0,0,.08); border-left: 4px solid #8B1A1A;
    }
    .card h2 { font-size: 18px; color: #8B1A1A; margin: 0 0 24px; }

    .form-group { margin-bottom: 18px; }
    label { display: block; font-size: 12px; font-weight: 700; color: #555;
            text-transform: uppercase; letter-spacing: .4px; margin-bottom: 6px; }
    input, textarea {
      width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 6px;
      font-size: 14px; font-family: Arial, sans-serif; outline: none;
      transition: border .2s; box-sizing: border-box;
    }
    input:focus, textarea:focus { border-color: #8B1A1A; box-shadow: 0 0 0 3px rgba(139,26,26,.08); }
    textarea { height: 100px; resize: vertical; }

    .btn-bar { display: flex; gap: 12px; margin-top: 8px; }
    .btn {
      display: inline-block; padding: 10px 20px; border-radius: 6px;
      font-size: 14px; font-weight: 700; text-decoration: none; cursor: pointer;
      border: none; font-family: Arial, sans-serif;
    }
    .btn-primary { background: #8B1A1A; color: #fff; }
    .btn-primary:hover { background: #a52020; }
    .btn-back { background: #eee; color: #333; }
    .btn-back:hover { background: #ddd; }
  </style>
</head>
<body>

<div class="topbar">
  <h1>Clinique Pasteur – Administration</h1>
  <a href="index.php?ctrl=auth&action=logout">Déconnexion</a>
</div>

<div class="container">
  <div class="card">
    <h2><?= isset($item) ? '✏️ Modifier le questionnaire' : '➕ Nouveau questionnaire' ?></h2>

    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

      <div class="form-group">
        <label>Nom du questionnaire</label>
        <input type="text" name="nom" required placeholder="Ex : Qualité de vie cancer"
               value="<?= htmlspecialchars($item['nom'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Description (optionnel)</label>
        <textarea name="description" placeholder="Décrivez ce questionnaire..."><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
      </div>

      <div class="btn-bar">
        <button type="submit" class="btn btn-primary">
          <?= isset($item) ? 'Enregistrer les modifications' : 'Créer le questionnaire' ?>
        </button>
        <a href="index.php?ctrl=crud&action=list&entite=questionnaire" class="btn btn-back">Annuler</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>