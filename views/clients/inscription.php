<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription — Lapinou Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/styles/inscription.css">
   
</head>
<body>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$message = $message ?? '';
$redirect = $redirect ?? '';
?>

<?php if (!empty($message)) : ?>
<div class="alert <?= strpos($message,'✅') !== false ? 'alert-success' : 'alert-error' ?>">
  <i class="fas <?= strpos($message,'✅') !== false ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
  <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<?php if (!empty($redirect)) : ?>
<script>setTimeout(() => { window.location.href = '<?= htmlspecialchars($redirect) ?>'; }, 3000);</script>
<?php endif; ?>

<div class="card">

  <div class="logo-container">
    <div class="logo floating">
      <img src="../assets/images/image.png" alt="Lapinou Store">
    </div>
    <h2>Créez votre compte</h2>
    <p class="subtitle">Rejoignez la communauté Lapinou Store</p>
  </div>

  <!-- Barre de progression -->
  <div class="progress-wrap">
    <div class="progress-seg done" id="seg1"></div>
    <div class="progress-seg" id="seg2"></div>
    <div class="progress-seg" id="seg3"></div>
    <div class="progress-seg" id="seg4"></div>
  </div>
  <div class="step-info">
    <span class="step-name" id="stepName">Identité</span>
    <span class="step-count" id="stepCount">Étape 1 / 4</span>
  </div>

  <form id="mainForm" method="POST" action="" novalidate>
    <input type="hidden" name="sai_role" value="clients">

    <div class="steps-container">

      <!-- ÉTAPE 1 -->
      <div class="step active" id="step1">
        <div class="form-group">
          <i class="fas fa-user fi"></i>
          <input type="text" name="sai_prenom" id="sai_prenom" placeholder="Prénom">
          <span class="err-msg" id="err_prenom">Veuillez saisir votre prénom.</span>
        </div>
        <div class="form-group">
          <i class="fas fa-user fi"></i>
          <input type="text" name="sai_nom" id="sai_nom" placeholder="Nom de famille">
          <span class="err-msg" id="err_nom">Veuillez saisir votre nom.</span>
        </div>
        <div class="form-actions">
          <button type="button" class="btn-primary" onclick="goNext(1)">
            Continuer &nbsp;<i class="fas fa-arrow-right" style="font-size:13px;"></i>
          </button>
        </div>
      </div>

      <!-- ÉTAPE 2 -->
      <div class="step" id="step2">
        <div class="form-group">
          <i class="fas fa-envelope fi"></i>
          <input type="email" name="sai_email" id="sai_email" placeholder="Adresse email">
          <span class="err-msg" id="err_email">Veuillez saisir un email valide.</span>
        </div>
        <div class="form-group">
          <i class="fas fa-map-marker-alt fi"></i>
          <input type="text" name="sai_adress" id="sai_adress" placeholder="Ville + Quartier">
          <span class="err-msg" id="err_adress">Veuillez indiquer votre localisation.</span>
        </div>
        <div class="form-actions">
          <div class="btn-row">
            <button type="button" class="btn-secondary" onclick="goBack(2)">
              <i class="fas fa-arrow-left" style="font-size:13px;"></i> &nbsp;Retour
            </button>
            <button type="button" class="btn-primary" onclick="goNext(2)">
              Continuer &nbsp;<i class="fas fa-arrow-right" style="font-size:13px;"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- ÉTAPE 3 -->
      <div class="step" id="step3">
        <div class="info-box">
          <i class="fas fa-shield-alt"></i>
          <span>Votre numéro ne sera jamais partagé avec des tiers.</span>
        </div>
        <div class="form-group">
          <i class="fas fa-phone fi"></i>
          <input type="tel" name="sai_numero" id="sai_numero" placeholder="Téléphone">
          <span class="err-msg" id="err_numero">Veuillez saisir votre numéro de téléphone.</span>
        </div>
        <div class="form-actions">
          <div class="btn-row">
            <button type="button" class="btn-secondary" onclick="goBack(3)">
              <i class="fas fa-arrow-left" style="font-size:13px;"></i> &nbsp;Retour
            </button>
            <button type="button" class="btn-primary" onclick="goNext(3)">
              Continuer &nbsp;<i class="fas fa-arrow-right" style="font-size:13px;"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- ÉTAPE 4 -->
      <div class="step" id="step4">
        <div class="form-group">
          <i class="fas fa-lock fi"></i>
          <input type="password" name="sai_password" id="sai_password" placeholder="Mot de passe (min. 4 car.)" oninput="checkStrength()">
          <span class="password-toggle" id="toggle1" onclick="togglePw('sai_password','toggle1')">
            <i class="fas fa-eye"></i>
          </span>
          <span class="err-msg" id="err_password">Mot de passe trop court (min. 4 caractères).</span>
        </div>
        <div class="strength-row">
          <div class="strength-seg" id="s1"></div>
          <div class="strength-seg" id="s2"></div>
          <div class="strength-seg" id="s3"></div>
          <div class="strength-seg" id="s4"></div>
        </div>
        <div class="strength-label" id="strengthLabel" style="margin-bottom:12px;">&nbsp;</div>

        <div class="form-group">
          <i class="fas fa-lock fi"></i>
          <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmer le mot de passe">
          <span class="password-toggle" id="toggle2" onclick="togglePw('confirm_password','toggle2')">
            <i class="fas fa-eye"></i>
          </span>
          <span class="err-msg" id="err_confirm">Les mots de passe ne correspondent pas.</span>
        </div>

        <div class="form-actions">
          <div class="btn-row">
            <button type="button" class="btn-secondary" onclick="goBack(4)">
              <i class="fas fa-arrow-left" style="font-size:13px;"></i> &nbsp;Retour
            </button>
            <button type="submit" name="btn_inscription" class="btn-primary">
              <i class="fas fa-user-plus" style="font-size:13px;"></i> &nbsp;S'inscrire
            </button>
          </div>
        </div>
      </div>

    </div><!-- /steps-container -->

    <!-- Écran succès (PHP) -->
    <div class="success-screen" id="successScreen">
      <div class="success-icon">
        <i class="fas fa-check"></i>
      </div>
      <h2>Compte créé !</h2>
      <p class="subtitle">Bienvenue dans la communauté Lapinou Store.<br>Redirection en cours…</p>
      <div class="pulse-dot"></div>
    </div>

  </form>

  <div class="link-container">
    <a href="connexion" class="link">Déjà un compte ? Se connecter</a>
  </div>

</div>

<script>
  let current = 1;
  const stepNames  = ['Identité','Coordonnées','Téléphone','Mot de passe'];

  function updateUI(n) {
    for (let i = 1; i <= 4; i++)
      document.getElementById('seg'+i).classList.toggle('done', i <= n);
    document.getElementById('stepName').textContent  = stepNames[n-1];
    document.getElementById('stepCount').textContent = 'Étape '+n+' / 4';
  }

  function showStep(n, dir) {
    document.getElementById('step'+current).classList.remove('active');
    const el = document.getElementById('step'+n);
    el.classList.remove('back');
    if (dir === 'back') el.classList.add('back');
    el.classList.add('active');
    current = n;
    updateUI(n);
  }

  function setErr(inputId, errId, show) {
    document.getElementById(inputId).classList.toggle('err-state', show);
    document.getElementById(errId).classList.toggle('show', show);
  }

  function goNext(step) {
    let ok = true;
    if (step === 1) {
      const p = document.getElementById('sai_prenom').value.trim();
      const n = document.getElementById('sai_nom').value.trim();
      setErr('sai_prenom','err_prenom',!p);
      setErr('sai_nom','err_nom',!n);
      ok = !!(p && n);
    } else if (step === 2) {
      const e = document.getElementById('sai_email').value.trim();
      const a = document.getElementById('sai_adress').value.trim();
      const eOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e);
      setErr('sai_email','err_email',!eOk);
      setErr('sai_adress','err_adress',!a);
      ok = !!(eOk && a);
    } else if (step === 3) {
      const t = document.getElementById('sai_numero').value.trim();
      setErr('sai_numero','err_numero',!t);
      ok = !!t;
    }
    if (ok && step < 4) showStep(step+1,'forward');
  }

  function goBack(step) {
    if (step > 1) showStep(step-1,'back');
  }

  function togglePw(id, toggleId) {
    const inp = document.getElementById(id);
    const isText = inp.type === 'text';
    inp.type = isText ? 'password' : 'text';
    document.querySelector('#'+toggleId+' i').className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
  }

  function checkStrength() {
    const pw = document.getElementById('sai_password').value;
    let score = 0;
    if (pw.length >= 4) score++;
    if (pw.length >= 8) score++;
    if (/[A-Z]/.test(pw) && /[0-9]/.test(pw)) score++;
    if (/[^a-zA-Z0-9]/.test(pw)) score++;
    const colors = ['#F44336','#FF9800','#00C853','#007E33'];
    const labels = ['Faible','Moyen','Fort','Très fort'];
    for (let i = 1; i <= 4; i++)
      document.getElementById('s'+i).style.background = i <= score ? colors[score-1] : 'var(--gray)';
    const lbl = document.getElementById('strengthLabel');
    lbl.textContent  = pw.length > 0 ? labels[score-1] : '\u00a0';
    lbl.style.color  = score > 0 ? colors[score-1] : '#aaa';
  }

  document.getElementById('mainForm').addEventListener('submit', function(e) {
    const p1 = document.getElementById('sai_password').value;
    const p2 = document.getElementById('confirm_password').value;
    let ok = true;
    if (p1.length < 4) { setErr('sai_password','err_password',true);  ok = false; }
    else                  setErr('sai_password','err_password',false);
    if (p1 !== p2)       { setErr('confirm_password','err_confirm',true);  ok = false; }
    else                  setErr('confirm_password','err_confirm',false);
    if (!ok) e.preventDefault();
  });

  document.querySelectorAll('input').forEach(inp => {
    inp.addEventListener('input', function() { this.classList.remove('err-state'); });
  });

  setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => {
      a.style.opacity = '0';
      setTimeout(() => a.remove(), 500);
    });
  }, 5000);
</script>

<?php if (!empty($redirect)) : ?>
<script>
  document.querySelector('.steps-container').style.display = 'none';
  document.querySelector('.progress-wrap').style.display   = 'none';
  document.querySelector('.step-info').style.display       = 'none';
  document.getElementById('successScreen').classList.add('show');
</script>
<?php endif; ?>

</body>
</html>