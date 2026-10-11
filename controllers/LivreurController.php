<?php
require_once "Models/livreur.php";

class LivreurController {
    private $model;
    private $livreur_nom;

    public function __construct() {
        try {
            $this->model = new LivreurModel();
            $this->livreur_nom = $this->getLivreurName(); // Méthode à implémenter
        } catch (Exception $e) {
            error_log("Erreur initialisation LivreurController: " . $e->getMessage());
            throw $e;
        }
    }

    public function Commandes() {
        try {
            // Vérification initiale
            if (!$this->model) {
                throw new Exception("Modèle non initialisé");
            }
            
            error_log("Récupération des commandes...");
            
            $commandes = $this->model->getAllCommandes();
            
            // Debug
            error_log("Type de retour: " . gettype($commandes));
            if (is_array($commandes)) {
                error_log("Nombre de commandes: " . count($commandes));
                if (!empty($commandes)) {
                    error_log("Première commande: " . json_encode($commandes[0]));
                }
            }
            
            // Préparation des données pour la vue
            $viewData = [
                'title' => "Gestion des Commandes",
                'message' => empty($commandes) ? "ℹ️ Aucune commande trouvée" : "",
                'commandes' => is_array($commandes) ? $commandes : [],
                'currentPage' => 1,
                'totalPages' => 1,
                'livreur_nom' => $this->livreur_nom
            ];
            $stats = [
        'today' => 5,        // Nombre de livraisons aujourd'hui
        'success_rate' => 92, // Taux de réussite en pourcentage
        'note' => 4.5         // Note moyenne
    ];
            extract($viewData);
            require "views/livreurs/livreur.php";
            
        } catch (Exception $e) {
            error_log("Erreur dans Commandes(): " . $e->getMessage());
            
            // Vue d'erreur
            $errorData = [
                'title' => "Erreur",
                'message' => "❌ " . $e->getMessage(),
                'commandes' => [],
                'currentPage' => 1,
                'totalPages' => 1
            ];
            
            extract($errorData);
            require "views/livreurs/livreur.php";
        }
    }

    private function getLivreurName(): string {
        // Implémentez votre logique de récupération du nom
        return $_SESSION['livreur_nom'] ?? 'Livreur';
    }
}