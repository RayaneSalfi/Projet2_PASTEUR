<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion – Clinique Pasteur</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#f9e5e5,#f1f3f5);min-height:100vh;display:flex;align-items:center;justify-content:center}
  .box{background:#fff;border-radius:14px;padding:40px;width:380px;box-shadow:0 8px 32px rgba(139,26,26,.12)}
  .logo{text-align:center;margin-bottom:24px}
  .logo h1{font-size:22px;font-weight:800;color:#8B1A1A}
  .logo p{font-size:13px;color:#888;margin-top:4px}
  .form-group{margin-bottom:16px}
  label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px}
  input{width:100%;padding:11px 14px;border:1px solid #ddd;border-radius:7px;font-size:14px;font-family:'Inter',sans-serif;outline:none;transition:border .2s}
  input:focus{border-color:#8B1A1A;box-shadow:0 0 0 3px rgba(139,26,26,.08)}
  .btn{width:100%;padding:12px;background:#8B1A1A;color:#fff;border:none;border-radius:7px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;transition:background .2s;margin-top:4px}
  .btn:hover{background:#a52020}
  .error{background:#fdecea;color:#8B1A1A;border:1px solid #f5c0c0;border-radius:7px;padding:10px 14px;font-size:13px;margin-bottom:16px}
</style>
</head>
<body>
<div class="box">
  <div class="logo">
    <h1>🏥 Clinique Pasteur</h1>
    <p>Espace sécurisé – Connexion</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="error">❌ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="index.php?ctrl=auth&action=login">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))) ?>">
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required autofocus placeholder="votre@email.fr">
    </div>
    <div class="form-group">
      <label>Mot de passe</label>
      <input type="password" name="password" required placeholder="••••••••">
    </div>
    <button type="submit" class="btn">Se connecter →</button>
  </form>
</div>
</body>
</html>