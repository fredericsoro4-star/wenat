<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adorès CMS | Page Non Trouvée</title>
  <link rel="icon" href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/assets/logo.png" type="image/x-icon"/>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f0f0f0;
    }
    .container {
      text-align: center;
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .container h1 {
      font-size: 48px;
      margin-bottom: 20px;
      color: #333;
    }
    .container p {
      font-size: 18px;
      margin-bottom: 30px;
      color: #666;
    }
    .container .error-icon {
      font-size: 64px;
      color: #e74c3c;
      margin-bottom: 20px;
    }
    .button {
      display: inline-block;
      padding: 12px 24px;
      margin: 10px;
      font-size: 18px;
      font-weight: bold;
      color: #2593B6;
      background-color: #fff;
      border: 2px solid #2593B6;
      border-radius: 30px;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s, transform 0.3s;
    }
    .button:hover {
      background-color: #2593B6;
      color: #fff;
      transform: translateY(-3px);
    }
    .button:active {
      transform: translateY(1px);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="error-icon">⚠️</div>
    <h1>4 0 4</h1>
    <p>Page non trouvée</p>
    <a href="<?php if(substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])),-1) =="/"){ echo (substr(((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"])), 0,-1)); }else{ echo ((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].dirname($_SERVER["PHP_SELF"]));} ?>/utilisateur/dashboard" class="button">Retour au tableau de bord</a>
 
  </div>
</body>
</html>