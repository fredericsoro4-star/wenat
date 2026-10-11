<?php
require_once 'Models/administrateur.php';

class CommandeController {
    public $admin;
    public $message = '';

    public function __construct() {
        $this->admin = new administrateur();
    }
    
    // Générer un token CSRF
    private function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    // Vérifier un token CSRF
    private function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public function details() {
        try {
            // Vérifier que l'utilisateur est admin
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                throw new Exception("Accès refusé");
            }
            
            // Récupérer l'ID de la commande
            $commande_id = $_GET['commande_id'] ?? null;
            
            if (!$commande_id) {
                throw new Exception("ID de commande manquant");
            }
            
            // Récupérer les détails de la commande
            $details = $this->admin->getCommandeDetails($commande_id);
            
            if (!$details) {
                throw new Exception("Commande non trouvée");
            }
            
            // Variables pour la vue
            $commande = $details['commande'];
            $produits = $details['produits'];
            $title = "Détails de la commande #" . $commande['order_id'];
            
            include "views/Administrateur/commande_details.php";
            
        } catch (Exception $e) {
            error_log("Erreur dans CommandeController::details(): " . $e->getMessage());
            
            $title = "Erreur";
            $message = "❌ " . $e->getMessage();
            $commande = [];
            $produits = [];
            
            include "views/Administrateur/commande_details.php";
        }
    }
}
?>