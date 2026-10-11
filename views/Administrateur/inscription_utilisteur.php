<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - Lapinou Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #00C853;
      --primary-light: #5EFC82;
      --primary-dark: #009624;
      --white: #FFFFFF;
      --light-bg: #F5FFF7;
      --text: #2E3D49;
      --gray: #E0E0E0;
      --shadow: 0 4px 12px rgba(0, 200, 83, 0.1);
      --transition: all 0.3s ease;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: var(--light-bg);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
      color: var(--text);
    }

    .form-container {
      width: 100%;
      max-width: 400px;
      padding: 20px;
    }

    .logo-container {
      text-align: center;
      margin-bottom: 25px;
    }

    .logo {
      width: 80px;
      height: 80px;
      margin: 0 auto 15px;
      border-radius: 50%;
      background: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border: 2px solid var(--primary-light);
    }

    .logo img {
      width: 70%;
      height: auto;
    }

    h1 {
      font-size: 22px;
      margin-bottom: 5px;
      color: var(--primary-dark);
    }

    .form-group {
      margin-bottom: 18px;
      position: relative;
    }

    .form-group i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--primary);
      font-size: 16px;
    }

    input, select {
      width: 100%;
      padding: 13px 15px 13px 45px;
      border: 1px solid var(--gray);
      border-radius: 20px;
      font-size: 15px;
      font-family: inherit;
      transition: var(--transition);
      background: var(--white);
      appearance: none;
    }

    input:focus, select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 200, 83, 0.2);
    }

    select {
      cursor: pointer;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2300C853' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 15px center;
      background-size: 12px;
    }

    .btn-submit {
      width: 100%;
      padding: 14px;
      background: linear-gradient(to right, var(--primary), var(--primary-dark));
      color: var(--white);
      border: none;
      border-radius: 30px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      margin-top: 10px;
      box-shadow: 0 4px 15px rgba(0, 200, 83, 0.3);
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 200, 83, 0.4);
    }

    .message {
      font-size: 14px;
      padding: 12px;
      margin-top: 20px;
      border-radius: 8px;
      text-align: center;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .success {
      background: rgba(0, 200, 83, 0.1);
      color: var(--primary-dark);
      border-left: 4px solid var(--primary);
    }

    .error {
      background: rgba(255, 88, 88, 0.1);
      color: #FF5858;
      border-left: 4px solid #FF5858;
    }

    @media (max-width: 480px) {
      .form-container {
        padding: 15px;
      }
      
      input, select {
        padding: 12px 12px 12px 40px;
      }
    }
  </style>
</head>
<body>
  <div class="form-container">
    <div class="logo-container">
      <div class="logo">
        <img src="../assests/images/image.png" alt="Lapinou Store">
      </div>
      <h1>Créer un compte</h1>
    </div>

    <form id="registrationForm" method="POST">
      <div class="form-group">
        <i class="fas fa-user"></i>
        <input type="text" name="sai_prenom" placeholder="Prénom" required>
      </div>
      
      <div class="form-group">
        <i class="fas fa-user"></i>
        <input type="text" name="sai_nom" placeholder="Nom" required>
      </div>
      
      <div class="form-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="sai_email" placeholder="Email" required>
      </div>
      
      <div class="form-group">
        <i class="fas fa-phone"></i>
        <input type="tel" name="sai_numero" placeholder="Téléphone" required>
      </div>
      
      <div class="form-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="sai_password" placeholder="Mot de passe" required>
      </div>
      
      <div class="form-group">
        <i class="fas fa-user-tag"></i>
        <select name="sai_role" required>
          <option value="" disabled selected>Sélectionnez votre rôle</option>
          <option value="admin">Administrateur</option>
          <option value="clients">Clients</option>
          <option value="employee">Employé</option>
          <option value="delivery">Livreur</option>
        </select>
      </div>
      
      <button class="btn-submit" name="btn_inscription">S'inscrire</button>
    </form>

    <div id="message" class="message" style="display: none;"></div>
  </div>
</body>
</html>