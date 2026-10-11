<?php 

 require "database/connexion.php";

try {
    $stmt = $this->con->prepare("SELECT COUNT(*) AS total FROM commandes WHERE statut = 'en attente'");
    $stmt->execute();
    $row = $stmt->fetch();
    $totalCommandesEnAttente = $row['total'];

    // On le met en variable globale si tu veux l’utiliser ailleurs
    $GLOBALS['totalCommandesEnAttente'] = $totalCommandesEnAttente;
} catch (PDOException $e) {
    $GLOBALS['totalCommandesEnAttente'] = 0;
}


try {
    $stmt = $this->con->prepare("SELECT COUNT(*) AS total FROM commande_rapide WHERE status = 'en_attente'");
    $stmt->execute();
    $row = $stmt->fetch();
    $totalCommandesRapidesEnAttente = $row['total'];
} catch (PDOException $e) {
    $totalCommandesRapidesEnAttente = 0;
}

// Maintenant tu peux utiliser ces deux variables séparément
// echo "Commandes classiques en attente : " . $totalCommandesEnAttente . "<br>";
// echo "Commandes rapides en attente : " . $totalCommandesRapidesEnAttente;

// Variables pour la pagination
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$totalPages = 1; // À calculer selon votre logique de pagination
$title = "Gestion des Commandes - Wenat";

session_start();

// Vérifier si l'utilisateur est connecté et a les permissions
if (!isset($_SESSION['user']['id']) || !isset($_SESSION['user']['role'])) {
    header('Location: /projets/lapin/clientController/connexion');
    exit();
}

// Vérifier si l'utilisateur a le rôle approprié
$allowed_roles = ['admin', 'manager'];
if (!in_array($_SESSION['user']['role'], $allowed_roles)) {
    header('Location: /projets/lapin/../Views/error.php');
    exit();
}
 // Compter les commandes en attente
// $stmt = $this->con->query("SELECT COUNT(*) AS en_attente FROM commandes_rapides WHERE status = 'en_attente'");
// $stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Affichage
// echo "Commandes en attente : " . $stats['en_attente'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tableau de bord Admin - Wenat</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --color-1: #FF6B6B;
      --color-2: #3dff02ff;
      --color-3: #deff66ff;
      --color-4: #7E57C2;
      --color-5: #42A5F5;
      --color-6: #FF9E40;
      --color-7: #AB47BC;
      --color-8: #26A69A;
      --color-9: #EF5350;
      --color-10: #5C6BC0;
      --text: #2C3E50;
      --light: #F9F7F7;
      --white: #FFFFFF;
      --cream: #FFF5E0;
      --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      --shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.15);
      --radius: 20px;
      --radius-sm: 12px;
      --transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      --transition-fast: all 0.25s ease-out;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
      color: var(--text);
      line-height: 1.6;
      overflow-x: hidden;
    }

    /* Header */
    .admin-header {
      background: linear-gradient(90deg, var(--color-4), var(--color-10));
      padding: 18px 35px;
      position: sticky;
      top: 0;
      z-index: 1000;
      transition: var(--transition);
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .admin-header.scrolled {
      padding: 15px 35px;
    }
#total-commandes-count {
    background: #ffcc00;      /* jaune doux */
    color: #222;              /* texte sombre */
    padding: 6px 12px;
    border-radius: 20px;      /* arrondi */
    font-weight: bold;
    font-size: 16px;
    display: inline-block;
    min-width: 30px;
    text-align: center;
}
    .header-container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo-container {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .logo {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--color-1), var(--color-6));
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
      border: 2px solid var(--white);
      transition: var(--transition);
      overflow: hidden;
    }

    .logo img {
      width: 80%;
      height: auto;
      transition: var(--transition);
      
    }

    .logo:hover {
      transform: rotate(15deg) scale(1.08);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .brand {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
      font-weight: 700;
      color: var(--white);
      letter-spacing: 0.5px;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 18px;
    }

    /* Boutons */
    .btn {
      padding: 12px 22px;
      border-radius: var(--radius-sm);
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      border: none;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      position: relative;
      overflow: hidden;
    }

    .btn-outline {
      background: transparent;
      border: 2px solid var(--white);
      color: var(--white);
    }

    .btn-outline:hover {
      background: var(--white);
      color: var(--color-4);
      transform: translateY(-3px);
      box-shadow: var(--shadow-hover);
    }

    /* User profile */
    .user-profile {
      display: flex;
      align-items: center;
      gap: 15px;
      background: rgba(255, 255, 255, 0.2);
      padding: 10px 20px;
      border-radius: var(--radius-sm);
      border: 1px solid rgba(255, 255, 255, 0.3);
      transition: var(--transition);
      backdrop-filter: blur(10px);
    }

    .user-profile:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      background: rgba(255, 255, 255, 0.3);
    }

    .user-avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--color-2), var(--color-8));
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      font-size: 16px;
    }

    .user-name {
      font-weight: 600;
      color: var(--white);
      font-size: 15px;
    }

    /* Main container */
    .admin-container {
      max-width: 1400px;
      margin: 40px auto;
      padding: 0 25px;
    }

    /* Dashboard header */
    .dashboard-header {
      display: flex;
      align-items: center;
      gap: 30px;
      margin-bottom: 40px;
      background: linear-gradient(120deg, var(--color-5), var(--color-10));
      border-radius: var(--radius);
      padding: 35px;
      box-shadow: var(--shadow);
      color: white;
      position: relative;
      overflow: hidden;
    }

    .dashboard-header::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
      transform: rotate(30deg);
    }

    .dashboard-logo {
      width: 85px;
      height: 85px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--color-6), var(--color-9));
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
      border: 3px solid rgba(255, 255, 255, 0.5);
      overflow: hidden;
      z-index: 2;
    }

    .dashboard-logo img {
      width: 85%;
      height: auto;
 
    }

    .dashboard-title h1 {
      font-family: 'Playfair Display', serif;
      font-size: 32px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 15px;
      z-index: 2;
    }

    .dashboard-title h1 i {
      color: var(--color-3);
      font-size: 28px;
    }

    .dashboard-title p {
      opacity: 0.9;
      font-size: 16px;
      max-width: 600px;
      z-index: 2;
    }

    /* Stats cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 25px;
      margin-bottom: 40px;
    }

    .stat-card {
      background: var(--white);
      border-radius: var(--radius-sm);
      box-shadow: var(--shadow);
      padding: 30px 25px;
      text-align: center;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
    }

    .stat-card:nth-child(1)::before { background: linear-gradient(90deg, var(--color-1), var(--color-6)); }
    .stat-card:nth-child(2)::before { background: linear-gradient(90deg, var(--color-2), var(--color-8)); }
    .stat-card:nth-child(3)::before { background: linear-gradient(90deg, var(--color-9), var(--color-6)); }
    .stat-card:nth-child(4)::before { background: linear-gradient(90deg, var(--color-4), var(--color-7)); }

    .stat-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-hover);
    }

    .stat-card i {
      font-size: 36px;
      margin-bottom: 18px;
      transition: var(--transition);
    }

    .stat-card:nth-child(1) i { color: var(--color-1); }
    .stat-card:nth-child(2) i { color: var(--color-2); }
    .stat-card:nth-child(3) i { color: var(--color-9); }
    .stat-card:nth-child(4) i { color: var(--color-4); }

    .stat-card:hover i {
      transform: scale(1.2) translateY(-5px);
    }

    .stat-card h3 {
      margin-bottom: 12px;
      color: var(--text);
      opacity: 0.8;
      font-size: 14px;
      font-weight: 600;
    }

    .stat-card .value {
      font-size: 28px;
      font-weight: 800;
    }

    .stat-card:nth-child(1) .value { color: var(--color-1); }
    .stat-card:nth-child(2) .value { color: var(--color-2); }
    .stat-card:nth-child(3) .value { color: var(--color-9); }
    .stat-card:nth-child(4) .value { color: var(--color-4); }

    /* Actions grid */
    .actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
      gap: 25px;
      margin-bottom: 40px;
    }

    .action-card {
      background: var(--white);
      border-radius: var(--radius-sm);
      box-shadow: var(--shadow);
      padding: 30px 20px;
      text-align: center;
      text-decoration: none;
      color: var(--text);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .action-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
    }

    .action-card:nth-child(1)::before { background: var(--color-1); }
    .action-card:nth-child(2)::before { background: var(--color-2); }
    .action-card:nth-child(3)::before { background: var(--color-3); }
    .action-card:nth-child(4)::before { background: var(--color-4); }
    .action-card:nth-child(5)::before { background: var(--color-5); }
    .action-card:nth-child(6)::before { background: var(--color-6); }
    .action-card:nth-child(7)::before { background: var(--color-7); }
    .action-card:nth-child(8)::before { background: var(--color-8); }

    .action-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: var(--shadow-hover);
    }

    .action-card:hover i {
      transform: scale(1.2) translateY(-5px);
    }

    .action-card i {
      font-size: 36px;
      margin-bottom: 18px;
      transition: var(--transition);
    }

    .action-card:nth-child(1) i { color: var(--color-1); }
    .action-card:nth-child(2) i { color: var(--color-2); }
    .action-card:nth-child(3) i { color: var(--color-3); }
    .action-card:nth-child(4) i { color: var(--color-4); }
    .action-card:nth-child(5) i { color: var(--color-5); }
    .action-card:nth-child(6) i { color: var(--color-6); }
    .action-card:nth-child(7) i { color: var(--color-7); }
    .action-card:nth-child(8) i { color: var(--color-8); }

    .action-card h3 {
      margin-bottom: 10px;
      font-size: 16px;
      font-weight: 700;
      transition: var(--transition);
    }

    .action-card p {
      color: var(--text);
      opacity: 0.7;
      font-size: 13px;
      transition: var(--transition);
    }

    /* Recent activity */
    .recent-activity {
      background: var(--white);
      border-radius: var(--radius-sm);
      box-shadow: var(--shadow);
      padding: 35px 30px;
      margin-top: 40px;
      position: relative;
    }

    .recent-activity::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, var(--color-7), var(--color-9));
      border-radius: var(--radius-sm) var(--radius-sm) 0 0;
    }

    .recent-activity h2 {
      color: var(--text);
      margin-bottom: 25px;
      font-size: 22px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-family: 'Playfair Display', serif;
    }

    .recent-activity h2 i {
      color: var(--color-7);
      font-size: 24px;
    }

    .activity-list {
      list-style: none;
    }

    .activity-item {
      display: flex;
      align-items: center;
      gap: 18px;
      padding: 18px 0;
      border-bottom: 1px solid rgba(0, 0, 0, 0.06);
      transition: var(--transition-fast);
    }

    .activity-item:hover {
      transform: translateX(8px);
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    .activity-icon {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 18px;
      transition: var(--transition);
      color: white;
    }

    .activity-item:nth-child(1) .activity-icon { background: var(--color-1); }
    .activity-item:nth-child(2) .activity-icon { background: var(--color-2); }
    .activity-item:nth-child(3) .activity-icon { background: var(--color-3); }
    .activity-item:nth-child(4) .activity-icon { background: var(--color-4); }
    .activity-item:nth-child(5) .activity-icon { background: var(--color-5); }

    .activity-item:hover .activity-icon {
      transform: scale(1.1) rotate(8deg);
    }

    .activity-content {
      flex: 1;
    }

    .activity-title {
      font-weight: 700;
      margin-bottom: 6px;
      color: var(--text);
      font-size: 15px;
    }

    .activity-time {
      color: var(--text);
      opacity: 0.7;
      font-size: 13px;
    }

    /* Menu mobile */
    .mobile-menu-btn {
      display: none;
      background: none;
      border: none;
      font-size: 28px;
      color: var(--white);
      cursor: pointer;
    }

    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(25px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-8px); }
      100% { transform: translateY(0px); }
    }

    .stat-card, .action-card, .recent-activity {
      animation: fadeIn 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
      opacity: 0;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    .action-card:nth-child(1) { animation-delay: 0.2s; }
    .action-card:nth-child(2) { animation-delay: 0.3s; }
    .action-card:nth-child(3) { animation-delay: 0.4s; }
    .action-card:nth-child(4) { animation-delay: 0.5s; }
    .action-card:nth-child(5) { animation-delay: 0.6s; }
    .action-card:nth-child(6) { animation-delay: 0.7s; }
    .action-card:nth-child(7) { animation-delay: 0.8s; }
    .action-card:nth-child(8) { animation-delay: 0.9s; }
    .recent-activity { animation-delay: 1s; }

    .stat-card i, .action-card i {
      animation: float 4s ease-in-out infinite;
    }

    .stat-card:nth-child(1) i { animation-delay: 0.5s; }
    .stat-card:nth-child(2) i { animation-delay: 0.7s; }
    .stat-card:nth-child(3) i { animation-delay: 0.9s; }
    .stat-card:nth-child(4) i { animation-delay: 1.1s; }

    /* Responsive */
    @media (max-width: 1024px) {
      .dashboard-title h1 {
        font-size: 28px;
      }
    }

    @media (max-width: 768px) {
      .admin-header {
        padding: 15px 25px;
      }
      
      .header-container {
        flex-wrap: wrap;
      }
      
      .mobile-menu-btn {
        display: block;
        order: 1;
      }
      
      .logo-container {
        order: 2;
        flex: 1;
        justify-content: center;
      }
      
      .header-actions {
        order: 3;
        width: 100%;
        margin-top: 15px;
        justify-content: center;
        display: none;
        flex-direction: column;
      }
      
      .header-actions.active {
        display: flex;
      }
      
      .dashboard-header {
        flex-direction: column;
        text-align: center;
        padding: 30px 25px;
        gap: 25px;
      }
      
      .dashboard-title p {
        margin: 0 auto;
      }
      
      .stats-grid, .actions-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 576px) {
      .admin-container {
        padding: 0 20px;
      }
      
      .stats-grid, .actions-grid {
        grid-template-columns: 1fr;
      }
      
      .dashboard-logo {
        width: 75px;
        height: 75px;
      }
      
      .dashboard-title h1 {
        font-size: 24px;
      }
      
      .recent-activity {
        padding: 25px 20px;
      }
      
      .activity-item {
        gap: 15px;
      }
      
      .activity-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
      }
      
      .btn {
        width: 100%;
        justify-content: center;
      }
      
      .user-profile {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="admin-header" id="main-header">
    <div class="header-container">
      <button class="mobile-menu-btn" id="mobile-menu">
        <i class="fas fa-bars"></i>
      </button>
      
      <div class="logo-container">
        <div class="logo">
          <img src="../assets/images/image.png" alt="Logo Wenat">
        </div>
        <span class="brand">Wenat</span>
      </div>
      
      <div class="header-actions" id="header-actions">
        <div class="user-profile">
          <div class="user-avatar">
            <?= strtoupper(substr($_SESSION['user']['prenom'], 0, 1)) ?>
          </div>
          <span class="user-name"><?= htmlspecialchars($_SESSION['user']['prenom']) ?></span>
        </div>
        <a href="../index.php" class="btn btn-outline">
          <i class="fas fa-store"></i> Voir le site
        </a>
        <a href="../ClientController/deconnexion" class="btn btn-outline">
          <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
      </div>
    </div>
  </header>

  <!-- Main content -->
  <div class="admin-container">
    <!-- Dashboard header -->
    <div class="dashboard-header">
      <div class="dashboard-logo">
        <img src="../assets/images/image.png" alt="Logo Wenat">
      </div>
      <div class="dashboard-title">
        <h1><i class="fas fa-tachometer-alt"></i> Tableau de Bord Administrateur</h1>
        <p>Gérez l'ensemble de votre élevage de lapins et votre boutique en ligne</p>
      </div>
    </div>

    <!-- Stats cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <i class="fas fa-paw"></i>
        <h3>Total produits</h3>
        <div class="value"><?= $stats['total_lapins'] ?? 0 ?></div>
      </div>
      
      <div class="stat-card">
        <i class="fas fa-check-circle"></i>
        <h3>En Stock</h3>
        <div class="value"><?= $stats['lapins_en_stock'] ?? 0 ?></div>
      </div>
      
      <div class="stat-card">
        <i class="fas fa-exclamation-triangle"></i>
        <h3>En Rupture</h3>
        <div class="value"><?= $stats['lapins_rupture'] ?? 0 ?></div>
      </div>
      
      <div class="stat-card">
        <i class="fas fa-money-bill-wave"></i>
        <h3>Prix Moyen</h3>
        <div class="value"><?= number_format($stats['prix_moyen'] ?? 0, 0) ?> FCFA</div>
      </div>
    </div>

    <!-- Actions grid -->
    <div class="actions-grid">
      <a href="../LapinController/ajouterLapin" class="action-card">
        <i class="fas fa-plus"></i>
        <h3>Ajouter</h3>
        <p>Nouveau lapin</p>
      </a>
      
      <a href="../AdministrateurController/inscription" class="action-card">
        <i class="fas fa-user-plus"></i>
        <h3>Inscription</h3>
        <p>Nouvel utilisateur</p>
      </a>

      <a href="../LapinController/listeLapins" class="action-card">
        <i class="fas fa-list"></i>
        <h3>produits</h3>
        <p>Liste des produits</p>
      </a>
      
      <a href="../LapinController/Commandes" class="action-card">
         <p>
          <span id="total-commandes-count"><?= $totalCommandesEnAttente ?></span>
         </p>
         <br>
        <i class="fas fa-shopping-cart"></i>
        
        <h3>Commandes</h3>
        <p>Gestion des commandes</p>
      </a>
      <a href=" ../views/Administrateur/commande_rapide.php" class="action-card">
        <span id="total-commandes-count"><?=$totalCommandesRapidesEnAttente ?></span>
        <br>
    <br>
        <i class="fas fa-shopping-cart"></i>
        
        <h3>Commandes</h3>

       
      
        <p>Gestion des commandes rapide</p>
      </a>
      
      <a href="../AdministrateurController/listeclients" class="action-card">
        <i class="fas fa-users"></i>
        <h3>Clients</h3>
        <p>Liste des clients</p>
      </a>
       
      <a href="" class="action-card">
        <i class="fas fa-user-tie"></i>
        <h3>Employés</h3>
        <p>Gestion des employés</p>
      </a>
      
      <a href="../AdministrateurController/listelivreurs" class="action-card">
        <i class="fas fa-truck"></i>
        <h3>Livreurs</h3>
        <p>Liste des livreurs</p>
      </a>

      <a href="../LivreurController/Commandes" class="action-card">
        <i class="fas fa-motorcycle"></i>
        <h3>Livreurs</h3>
        <p>Espace livreurs</p>
      </a>
    </div>

    <!-- Recent activity -->
    <div class="recent-activity">
      <h2><i class="fas fa-clock"></i> Activité Récente</h2>
      <ul class="activity-list">
        <?php if (!empty($lapins_recents)): ?>
          <?php foreach (array_slice($lapins_recents, 0, 5) as $lapin): ?>
            <li class="activity-item">
              <div class="activity-icon">
                <i class="fas fa-paw"></i>
              </div>
              <div class="activity-content">
                <div class="activity-title"><?= htmlspecialchars($lapin['nom']) ?></div>
                <div class="activity-time"><?= htmlspecialchars($lapin['race'] ?? 'N/A') ?> - <?= number_format($lapin['prix'], 0) ?> FCFA</div>
              </div>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-info-circle"></i>
            </div>
            <div class="activity-content">
              <div class="activity-title">Aucune activité récente</div>
              <div class="activity-time">Ajoutez un premier lapin pour commencer</div>
            </div>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <script>
    // Gestion du header au scroll
    const header = document.getElementById('main-header');
    
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });

    // Menu mobile
    const mobileMenuBtn = document.getElementById('mobile-menu');
    const headerActions = document.getElementById('header-actions');
    
    mobileMenuBtn.addEventListener('click', () => {
      headerActions.classList.toggle('active');
      
      if (headerActions.classList.contains('active')) {
        mobileMenuBtn.innerHTML = '<i class="fas fa-times"></i>';
      } else {
        mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
      }
    });

    // Animation des cartes lorsqu'elles apparaissent à l'écran
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.animationPlayState = 'running';
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.stat-card, .action-card, .recent-activity').forEach(card => {
      observer.observe(card);
    });
  </script>
</body>
</html>