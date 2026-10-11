<?php
// Désactiver l'affichage des erreurs en production
ini_set('display_errors', 0);
error_reporting(0);

// Définir le chemin racine pour tous les includes
define('ROOT_PATH', __DIR__);

// Autoloader pour les modèles
spl_autoload_register(function ($class_name) {
    $model_file = ROOT_PATH . '/models/' . strtolower($class_name) . '.php';
    if (file_exists($model_file)) {
        require_once $model_file;
    }
});

// Chargement automatique des contrôleurs
$controllers_dir = "controllers";
if (is_dir($controllers_dir)) {
    $fichiers = scandir($controllers_dir);
    for ($i = 2; $i < count($fichiers); $i++) {
        $fichier = $controllers_dir . "/" . $fichiers[$i];
        if (is_file($fichier) && pathinfo($fichier, PATHINFO_EXTENSION) === 'php') {
            require_once $fichier;
        }
    }
} else {
    die("Dossier controllers introuvable");
}

// Récupération des paramètres
$controller = $_GET['c'] ?? 'home'; // Contrôleur par défaut
$action = $_GET['a'] ?? 'index';    // Action par défaut

// Gestion du routing
if (class_exists($controller)) {
    $instance = new $controller();
    
    if (method_exists($instance, $action)) {
        $instance->$action();
    } else {
        // Action n'existe pas, essayer une action par défaut
        if (method_exists($instance, 'index')) {
            $instance->index();
        } else {
            handleError("Action '$action' introuvable dans le contrôleur '$controller'");
        }
    }
} else {
    handleError("Contrôleur '$controller' introuvable");
}

function handleError($message = "") {
    // Log l'erreur
    error_log("Router Error: " . $message);
    
    // Essayer le contrôleur d'erreur
    if (class_exists('ErrorController')) {
        $error = new ErrorController();
        if (method_exists($error, 'page')) {
            $error->page();
        } else {
            showSimpleError($message);
        }
    } else {
        showSimpleError($message);
    }
}

function showSimpleError($message = "") {
    http_response_code(404);
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Erreur 404</title>
    </head>
    <body>
        <h1>Page non trouvée</h1>
        <p>La page demandée n'existe pas.</p>
        <a href='/'>Retour à l'accueil</a>
    </body>
    </html>";
    exit();
}
?>