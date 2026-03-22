<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Code 2FA – Clinique Pasteur</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#f9e5e5,#f1f3f5);min-height:100vh;display:flex;align-items:center;justify-content:center}
  .box{background:#fff;border-radius:14px;padding:40px;width:380px;box-shadow:0 8px 32px rgba(139,26,26,.12);text-align:center}
  h1{font-size:20px;font-weight:800;color:#8B1A1A;margin-bottom:6px}
  .sub{font-size:13px;color:#888;margin-bottom:24px}
  .debug-box{background:#fff3e0;border:1px solid #f39c12;border-radius:8px;padding:14px;margin-bottom:20px;font-size:13px;color:#7d4e00}
  .code-big{font-size:28px;font-weight:800;letter-spacing:8px;color:#8B1A1A;margin:8px 0}
  .form-group{margin-bottom:16px;text-align:left}
  label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px}
  input{width:100%;padding:14px;border:1px solid #ddd;border-radius:7px;font-size:24px;font-weight:800;letter-spacing:8px;text-align:center;font-family:'Inter',sans-serif;outline:none;transition:border .2s}
  input:focus{border-color:#8B1A1A;box-shadow:0 0 0 3px rgba(139,26,26,.08)}
  .btn{width:100%;padding:12px;background:#8B1A1A;color:#fff;border:none;border-radius:7px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;transition:background .2s}
  .btn:hover{background:#a52020}
  .back{display:block;margin-top:14px;font-size:13px;color:#8B1A1A;text-decoration:none;font-weight:600}
  .back:hover{text-decoration:underline}
  .error{background:#fdecea;color:#8B1A1A;border:1px solid #f5c0c0;border-radius:7px;padding:10px 14px;font-size:13px;margin-bottom:16px;text-align:left}
</style>
</head>
<body>
<div class="box">
  <h1>🔐 Vérification 2FA</h1>
  <p class="sub">Un code a été envoyé à votre adresse email</p>

  <?php if (!empty($error)): ?>
    <div class="error">❌ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($_SESSION['2fa_debug'])): ?>
    <div class="debug-box">
      🛠️ <strong>Mode développement</strong><br>
      Votre code 2FA :
      <div class="code-big"><?= $_SESSION['2fa_debug'] ?></div>
      <small>(En production ce code est envoyé par email)</small>
    </div>
  <?php endif; ?>

  <form method="POST" action="index.php?ctrl=auth&action=verify">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <div class="form-group">
      <label>Code à 6 caractères</label>
      <input type="text" name="code" maxlength="6" autofocus placeholder="______" autocomplete="off">
    </div>
    <button type="submit" class="btn">Valider le code →</button>
  </form>
  <a href="index.php?ctrl=auth&action=login" class="back">← Retour à la connexion</a>
</div>
</body>
</html>