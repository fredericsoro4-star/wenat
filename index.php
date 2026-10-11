<?php
session_start();

// Autoloader simple
function autoload($className) {
    $paths = [
        'controllers/' . $className . '.php',
        'Models/' . $className . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
}

spl_autoload_register('autoload');

// Configuration des routes
$routes = [
    // Pages principales
    '' => ['controller' => 'LapinController', 'action' => 'catalog'],
    'home' => ['controller' => 'LapinController', 'action' => 'catalog'],
    'catalog' => ['controller' => 'LapinController', 'action' => 'catalog'],
    
    // Gestion des lapins (Admin)
    'ajouter_lapin' => ['controller' => 'LapinController', 'action' => 'ajouterLapin'],
    'liste_lapins' => ['controller' => 'LapinController', 'action' => 'listeLapins'],
    'detail_lapin' => ['controller' => 'LapinController', 'action' => 'detailLapin'],
    'dashboard' => ['controller' => 'LapinController', 'action' => 'dashboard'],
    
    // Utilisateurs
    'inscription' => ['controller' => 'ClientController', 'action' => 'inscription'],
    'connexion' => ['controller' => 'ClientController', 'action' => 'connexion'],
    'valider_inscription' => ['controller' => 'ClientController', 'action' => 'validiteinscription'],
    'deconnexion' => ['controller' => 'ClientController', 'action' => 'deconnexion'],
    
    // Commandes et panier
    'panier' => ['controller' => 'ClientController', 'action' => 'panier'],
    'commander' => ['controller' => 'ClientController', 'action' => 'commander'],
    'mes_commandes' => ['controller' => 'ClientController', 'action' => 'mesCommandes'],
    'detail_commande' => ['controller' => 'ClientController', 'action' => 'detailCommande'],
    
    // Administration
    'admin_dashboard' => ['controller' => 'AdministrateurController', 'action' => 'dashboard'],
    'admin_utilisateurs' => ['controller' => 'AdministrateurController', 'action' => 'liste'],
    'admin_produits' => ['controller' => 'AdministrateurController', 'action' => 'listeproduits'],
    'admin_commandes' => ['controller' => 'AdministrateurController', 'action' => 'commandes'],
    
    // Pages spéciales
    'facture' => ['controller' => 'LapinController', 'action' => 'facture'],
    'paiement' => ['controller' => 'LapinController', 'action' => 'paiement'],
    'ventes' => ['controller' => 'AdministrateurController', 'action' => 'dashboard'],
    'livreur' => ['controller' => 'LapinController', 'action' => 'livreur'],
    
    // API
    'api_lapins' => ['controller' => 'LapinController', 'action' => 'apiGetLapins'],
    'api_lapin' => ['controller' => 'LapinController', 'action' => 'apiGetLapin'],
    'api_update_stock' => ['controller' => 'LapinController', 'action' => 'updateStock']
];

// Obtenir l'action depuis l'URL
$action = $_GET['action'] ?? '';

// Vérifier si la route existe
if (!isset($routes[$action])) {
    // Si pas de route trouvée, afficher la page d'accueil par défaut
    if (empty($action)) {
        include 'index.html';
        exit;
    } else {
        // Page 404
        http_response_code(404);
        echo "<h1>Page non trouvée</h1>";
        exit;
    }
}

// Obtenir le contrôleur et l'action
$controllerName = $routes[$action]['controller'];
$actionName = $routes[$action]['action'];

// Vérifier que le contrôleur existe
if (!class_exists($controllerName)) {
    die("Contrôleur $controllerName introuvable");
}

// Créer une instance du contrôleur
$controller = new $controllerName();

// Vérifier que l'action existe
if (!method_exists($controller, $actionName)) {
    die("Action $actionName introuvable dans $controllerName");
}

// Exécuter l'action
try {
    $controller->$actionName();
} catch (Exception $e) {
    error_log("Erreur dans $controllerName::$actionName - " . $e->getMessage());
    echo "<h1>Erreur interne</h1>";
    if (ini_get('display_errors')) {
        echo "<p>" . $e->getMessage() . "</p>";
    }
}
?>