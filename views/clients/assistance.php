<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signaler un problème - MonApp</title>
  <link rel="stylesheet" href="../../assets/styles/assistance.css">
   
</head>
<body>
  <div class="overlay">
    <header>
      <div class="container">
        <div class="header-content">
          <img src="../../assets/images/image.png" alt="Logo entreprise">
          <div class="logo">Wenat</div>
        </div>
      </div>
    </header>

    <section class="hero">
      <div class="container">
        <h1>Signaler un problème</h1>
        <p>Décrivez-nous l'anomalie que vous avez rencontrée pour que nous puissions la résoudre rapidement.</p>
      </div>
    </section>

    <section class="main-content">
      <div class="container">
        <div class="report-card">
          <form id="problemForm">
            <div class="form-group">
              <label for="name">Votre nom</label>
              <input type="text" id="name" required>
            </div>
            
            <div class="form-group">
              <label for="email">Votre email</label>
              <input type="email" id="email" required>
            </div>
            
            <div class="form-group">
              <label for="problemType">Type de problème</label>
              <select id="problemType" required>
                <option value="">Sélectionnez...</option>
                <option value="bug">Bug technique</option>
                <option value="performance">Lenteur</option>
                <option value="ui">Problème d'interface</option>
                <option value="other">Autre</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="description">Description du problème</label>
              <textarea id="description" placeholder="Décrivez le problème en détail..." required></textarea>
            </div>
            
            <button type="submit" class="btn btn-block">Envoyer le signalement</button>
          </form>
        </div>
        
        <div class="help-links">
          <h3>Autres ressources d'aide</h3>
          <ul>
            <li><a href="#">Centre d'aide en ligne</a></li>
            <li><a href="#">FAQ - Questions fréquentes</a></li>
            <li><a href="#">Contactez notre support</a></li>
            <li><a href="#">Statut des services</a></li>
          </ul>
        </div>
      </div>
    </section>

    <footer>
      <div class="container">
        <p>&copy; 2025 MonApp. Tous droits réservés.</p>
      </div>
    </footer>
  </div>

  <script>
    document.getElementById('problemForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;
      alert(`Merci ${name} ! Votre signalement a été envoyé. Nous vous répondrons à ${email} sous 24h.`);
      this.reset();
    });
  </script>
</body>
</html>
