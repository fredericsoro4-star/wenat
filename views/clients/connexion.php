<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - Lapinou Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="../assets/styles/authantification.css">
</head>
<body>
  <?php
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }
  
  if (isset($_SESSION['success_message'])) {
      echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i>'.htmlspecialchars($_SESSION['success_message']).'</div>';
      unset($_SESSION['success_message']);
  }
  
  if (!empty($message)) {
      echo '<div class="alert '. (strpos($message, '✅') !== false ? 'alert-success' : 'alert-error') .'">';
      echo '<i class="fas '. (strpos($message, '✅') !== false ? 'fa-check-circle' : 'fa-exclamation-circle') .'"></i>';
      echo htmlspecialchars($message);
      echo '</div>';
  }
  ?>

  <div class="card">
    <div class="logo-container">
      <div class="logo floating">
        <img src="../assets/images/image.png" alt="Lapinou Store">
      </div>
      <h2>Connectez-vous</h2>
      <p class="subtitle">Accédez à votre espace personnel</p>
    </div>
    
    <form id="formConnexion" method="POST" action="">
     <div class="form-group">
  <i class="fas fa-user"></i>
  <input type="text" name="sai_identifiant" placeholder="Email ou numéro de téléphone" required>
</div>
      <div class="form-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="sai_password" id="password" placeholder="Mot de passe"minlength="4" required>
        <span class="password-toggle" id="togglePassword">
          <i class="fas fa-eye"></i>
        </span>
      </div>
      <div class="remember-me">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Se souvenir de moi</label>
      </div>
      <div class="form-actions">
        <button type="submit" name="btn_connexion">
          <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>Se connecter
        </button>
      </div>
    </form>

    <div class="link-container">
      <a href="inscription" class="link">Pas encore de compte ? S'inscrire</a><br>
      <a href="../views/clients/motpasseoublier.php" class="link" style="margin-top: 10px;">Mot de passe oublié ?</a>
    </div>
  </div>

  <script>
    // Toggle password visibility
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    
    togglePassword.addEventListener('click', function() {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      });
    }, 5000);

    <?php if (!empty($this->shouldRedirect)): ?>
    // Countdown for redirection
    let seconds = 3;
    const countdownElement = document.createElement('div');
    countdownElement.style.textAlign = 'center';
    countdownElement.style.marginTop = '10px';
    countdownElement.style.fontSize = '0.9rem';
    countdownElement.style.color = 'var(--primary-dark)';
    countdownElement.textContent = `Redirection dans ${seconds} secondes...`;
    document.querySelector('.alert-success').appendChild(countdownElement);

    const countdown = setInterval(() => {
        seconds--;
        countdownElement.textContent = `Redirection dans ${seconds} seconde${seconds !== 1 ? 's' : ''}...`;
        if (seconds <= 0) {
            clearInterval(countdown);
            window.location.href = "../index.php";
        }
    }, 1000);
    <?php endif; ?>
  </script>
</body>
</html>