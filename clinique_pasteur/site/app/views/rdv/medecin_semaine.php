<?php
// views/rdv/medecin_semaine.php
// US4 – Rendez-vous de la semaine pour le médecin
// Clinique Pasteur – Rayan
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes rendez-vous – Semaine</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; color: #222; margin: 0; }
    .topbar {
      background: #8B1A1A; color: #fff; padding: 14px 32px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .topbar h1 { font-size: 18px; margin: 0; }
    .topbar .sub { font-size: 13px; opacity: .75; }
    .container { max-width: 1000px; margin: 32px auto; padding: 0 20px; }

    .semaine-header {
      background: #fff; border-radius: 8px; padding: 20px 24px;
      margin-bottom: 20px; border-left: 4px solid #8B1A1A;
      box-shadow: 0 1px 4px rgba(0,0,0,.08);
    }
    .semaine-header h2 { margin: 0 0 4px; font-size: 17px; color: #8B1A1A; }
    .semaine-header p  { margin: 0; color: #666; font-size: 14px; }

    table { width: 100%; border-collapse: collapse; background: #fff;
            border-radius: 8px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    th { background: #8B1A1A; color: #fff; padding: 12px 16px; text-align: left; font-size: 13px; }
    td { padding: 11px 16px; border-bottom: 1px solid #eee; font-size: 14px; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #fdf5f5; }

    .badge {
      display: inline-block; padding: 3px 9px; border-radius: 12px;
      font-size: 11px; font-weight: 700;
    }
    .badge-ok    { background: #d6edd6; color: #1A5C1A; }
    .badge-non   { background: #fdecea; color: #8B1A1A; }
    .badge-plan  { background: #e8f0f8; color: #1a3c6b; }
    .badge-annul { background: #f0f0f0; color: #666; }

    .btn { display: inline-block; padding: 5px 13px; border-radius: 5px;
           font-size: 12px; font-weight: 700; text-decoration: none; }
    .btn-primary { background: #8B1A1A; color: #fff; }
    .btn-primary:hover { background: #a52020; }

    .empty { text-align: center; padding: 40px; color: #999; font-size: 15px; }
    .stat-bar {
      display: flex; gap: 16px; margin-bottom: 20px;
    }
    .stat-card {
      flex: 1; background: #fff; border-radius: 8px; padding: 16px 20px;
      box-shadow: 0 1px 4px rgba(0,0,0,.08); text-align: center;
    }
    .stat-num  { font-size: 28px; font-weight: 800; color: #8B1A1A; }
    .stat-label{ font-size: 12px; color: #888; margin-top: 2px; }
  </style>
</head>
<body>

<div class="topbar">
  <div>
    <h1>Clinique Pasteur</h1>
    <div class="sub">Médecin connecté : <?= htmlspecialchars($_SESSION['nom'] ?? '') ?></div>
  </div>
  <div style="display:flex;gap:20px;align-items:center">
    <a href="index.php?ctrl=crud&action=list&entite=questionnaire" style="color:#fff;font-size:13px">📋 Questionnaires</a>
    <a href="index.php?ctrl=auth&action=logout" style="color:#fff;font-size:13px">Déconnexion</a>
  </div>
</div>

<div class="container">

  <div class="semaine-header">
    <h2>📅 Rendez-vous de la semaine</h2>
    <p>Du <strong><?= $debutSemaine ?></strong> au <strong><?= $finSemaine ?></strong></p>
  </div>

  <?php
    $total    = count($rdvs);
    $remplis  = count(array_filter($rdvs, fn($r) => $r['questionnaire_rempli'] > 0));
    $nonRemplis = $total - $remplis;
  ?>
  <div class="stat-bar">
    <div class="stat-card"><div class="stat-num"><?= $total ?></div><div class="stat-label">RDV cette semaine</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#1A5C1A"><?= $remplis ?></div><div class="stat-label">Questionnaires remplis</div></div>
    <div class="stat-card"><div class="stat-num" style="color:#c0392b"><?= $nonRemplis ?></div><div class="stat-label">Questionnaires manquants</div></div>
  </div>

  <?php if (empty($rdvs)): ?>
    <div class="empty">Aucun rendez-vous prévu cette semaine.</div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Date / Heure</th>
        <th>Patient (pseudonyme)</th>
        <th>Étude</th>
        <th>Statut RDV</th>
        <th>Questionnaire</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rdvs as $r): ?>
      <tr>
        <td><strong><?= date('d/m/Y', strtotime($r['date_rdv'])) ?></strong><br>
            <span style="color:#888;font-size:12px"><?= date('H:i', strtotime($r['date_rdv'])) ?></span></td>
        <td><?= htmlspecialchars($r['patient_pseudo']) ?></td>
        <td><?= htmlspecialchars($r['etude_nom'] ?? '—') ?></td>
        <td>
          <?php if ($r['statut'] === 'planifié'): ?>
            <span class="badge badge-plan">Planifié</span>
          <?php elseif ($r['statut'] === 'annulé'): ?>
            <span class="badge badge-annul">Annulé</span>
          <?php else: ?>
            <span class="badge"><?= htmlspecialchars($r['statut']) ?></span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($r['questionnaire_rempli'] > 0): ?>
            <span class="badge badge-ok">✓ Rempli</span>
            <?php if ($r['derniere_saisie']): ?>
              <br><span style="font-size:11px;color:#888">le <?= date('d/m', strtotime($r['derniere_saisie'])) ?></span>
            <?php endif; ?>
          <?php else: ?>
            <span class="badge badge-non">✗ Non rempli</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="index.php?ctrl=rdv&action=detail&id=<?= $r['id_rdv'] ?>" class="btn btn-primary">
            Voir détail
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

</div>
</body>
</html>