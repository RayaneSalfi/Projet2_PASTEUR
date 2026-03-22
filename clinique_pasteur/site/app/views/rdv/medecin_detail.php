<?php
// views/rdv/medecin_detail.php
// US4 – Détail d'un RDV : réponses questionnaire du patient
// Clinique Pasteur – Rayan
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Détail RDV – <?= htmlspecialchars($rdv['patient_pseudo']) ?></title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; color: #222; margin: 0; }
    .topbar { background: #8B1A1A; color: #fff; padding: 14px 32px; display: flex; align-items: center; gap: 12px; }
    .topbar a { color: #ffaaaa; font-size: 13px; text-decoration: none; }
    .topbar h1 { font-size: 17px; margin: 0; }
    .container { max-width: 900px; margin: 28px auto; padding: 0 20px; }

    .rdv-card {
      background: #fff; border-radius: 8px; padding: 20px 24px;
      margin-bottom: 24px; border-left: 4px solid #8B1A1A;
      box-shadow: 0 1px 4px rgba(0,0,0,.08);
      display: flex; gap: 32px; flex-wrap: wrap;
    }
    .rdv-field label { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #999; }
    .rdv-field p { margin: 2px 0 0; font-size: 15px; font-weight: 700; }

    .theme-block {
      background: #fff; border-radius: 8px; margin-bottom: 16px;
      box-shadow: 0 1px 4px rgba(0,0,0,.08); overflow: hidden;
    }
    .theme-header {
      background: #f9e5e5; padding: 10px 18px;
      font-weight: 700; font-size: 14px; color: #8B1A1A;
      border-bottom: 1px solid #f0c0c0;
    }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 9px 14px; font-size: 12px; color: #666; text-align: left;
         background: #fafafa; border-bottom: 1px solid #eee; }
    td { padding: 9px 14px; font-size: 13px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }

    .score-bar {
      display: flex; align-items: center; gap: 8px;
    }
    .bar-bg {
      flex: 1; height: 8px; background: #eee; border-radius: 4px; overflow: hidden;
    }
    .bar-fill { height: 100%; border-radius: 4px; }
    .bar-1 { background: #27ae60; }
    .bar-2 { background: #82c94f; }
    .bar-3 { background: #f39c12; }
    .bar-4 { background: #e67e22; }
    .bar-5 { background: #e74c3c; }
    .score-num { font-weight: 700; font-size: 14px; min-width: 20px; }

    .no-reponse { text-align: center; padding: 30px; color: #999; font-style: italic; }
    .back-btn {
      display: inline-block; margin-bottom: 16px; padding: 8px 16px;
      background: #8B1A1A; color: #fff; border-radius: 5px;
      text-decoration: none; font-size: 13px; font-weight: 700;
    }
  </style>
</head>
<body>

<div class="topbar">
  <h1>Clinique Pasteur</h1>
  <span style="opacity:.5">›</span>
  <a href="index.php?ctrl=rdv&action=semaine">Rendez-vous semaine</a>
  <span style="opacity:.5">›</span>
  <span>Détail RDV</span>
</div>

<div class="container">
  <a href="index.php?ctrl=rdv&action=semaine" class="back-btn">← Retour à la semaine</a>

  <div class="rdv-card">
    <div class="rdv-field">
      <label>Patient</label>
      <p><?= htmlspecialchars($rdv['patient_pseudo']) ?></p>
    </div>
    <div class="rdv-field">
      <label>Date du RDV</label>
      <p><?= date('d/m/Y à H:i', strtotime($rdv['date_rdv'])) ?></p>
    </div>
    <div class="rdv-field">
      <label>Étude</label>
      <p><?= htmlspecialchars($rdv['etude_nom'] ?? '—') ?></p>
    </div>
    <div class="rdv-field">
      <label>Statut</label>
      <p><?= htmlspecialchars($rdv['statut']) ?></p>
    </div>
  </div>

  <h3 style="margin-bottom:14px; color:#8B1A1A; font-size:16px">
    📋 Dernier questionnaire rempli par le patient
  </h3>

  <?php if (empty($reponsesParTheme)): ?>
    <div class="no-reponse">Ce patient n'a pas encore rempli de questionnaire.</div>
  <?php else: ?>
    <?php foreach ($reponsesParTheme as $theme => $questions): ?>
    <div class="theme-block">
      <div class="theme-header"><?= htmlspecialchars($theme) ?></div>
      <table>
        <thead>
          <tr><th style="width:60%">Question</th><th>Réponse</th><th style="width:30%">Score</th></tr>
        </thead>
        <tbody>
          <?php foreach ($questions as $q):
            $val = (int)$q['valeur'];
            $pct = ($val / 5) * 100;
            $colorClass = 'bar-' . $val;
            $labels = [1 => 'Pas du tout', 2 => 'Un peu', 3 => 'Modérément', 4 => 'Beaucoup', 5 => 'Extrêmement'];
          ?>
          <tr>
            <td><?= htmlspecialchars($q['question']) ?>
              <?php if ($q['inversee']): ?><span style="font-size:10px;color:#888"> (inversée)</span><?php endif; ?>
            </td>
            <td><?= $labels[$val] ?? $val ?></td>
            <td>
              <div class="score-bar">
                <span class="score-num"><?= $val ?>/5</span>
                <div class="bar-bg">
                  <div class="bar-fill <?= $colorClass ?>" style="width:<?= $pct ?>%"></div>
                </div>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>
</body>
</html>