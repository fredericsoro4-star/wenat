<?php
require_once "Models/produits.php";

class LapinController 
{
    private $db;
    private $admin;
    public $lapin;
    public $message = '';

    public function __construct()
    {
        $this->lapin = new Produits();
    }

    public function dashboard()
    {
        $stats = $this->lapin->getStatistiques();
        $lapins_recents = $this->lapin->listeProduits();
        include "views/Administrateur/dashboard.php";
    }

    public function ajouterLapin()
    {
        if (isset($_POST['btn_ajouter'])) {
            $this->lapin->nom         = trim($_POST['name'] ?? '');
            $this->lapin->race        = trim($_POST['breed'] ?? '');
            $this->lapin->age_mois    = (int)($_POST['age_months'] ?? 0);
            $this->lapin->poids_kg    = (float)($_POST['weight_kg'] ?? 0.0);
            $this->lapin->prix        = (float)($_POST['price'] ?? 0);
            $this->lapin->stock       = (int)($_POST['stock_quantity'] ?? 0);
            $this->lapin->description = trim($_POST['description'] ?? '');
            $this->lapin->photo       = trim($_POST['image_url'] ?? '');
            $this->lapin->is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $this->lapin->is_active   = isset($_POST['is_active']) ? 1 : 0;

            if (empty($this->lapin->nom) || empty($this->lapin->race) || 
                empty($this->lapin->description) || $this->lapin->prix <= 0) {
                $this->message = "❌ Tous les champs obligatoires doivent être remplis.";
            } else {
                try {
                    if ($this->lapin->ajouterProduit()) {
                        $this->message = "✅ Lapin ajouté avec succès !";
                        header("Location: " . $_SERVER['REQUEST_URI']);
                        exit();
                    } else {
                        $this->message = "❌ Erreur lors de l'ajout du lapin.";
                    }
                } catch (PDOException $e) {
                    $this->message = "❌ Erreur SQL : " . $e->getMessage();
                }
            }
        }

        include "views/Administrateur/ajoute_lapin.php";
    }

    public function listeLapins()
    {
        // Récupérer le message de session si présent
        if (isset($_SESSION['message'])) {
            $this->message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        // Traitement de la modification
        if (isset($_POST['btn_modifier'])) {
            $this->lapin->id          = $_POST['id'] ?? null;
            $this->lapin->nom         = $_POST['name'] ?? '';
            $this->lapin->race        = $_POST['breed'] ?? '';
            $this->lapin->age_mois    = $_POST['age_months'] ?? 0;
            $this->lapin->poids_kg    = $_POST['weight_kg'] ?? 0.0;
            $this->lapin->prix        = $_POST['price'] ?? 0;
            $this->lapin->stock       = $_POST['stock_quantity'] ?? 0;
            $this->lapin->description = $_POST['description'] ?? '';
            $this->lapin->photo       = $_POST['image_url'] ?? '';
            $this->lapin->is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $this->lapin->is_active   = isset($_POST['is_active']) ? 1 : 0;

            if ($this->lapin->modifierProduit()) {
                $this->message = "✅ Lapin modifié avec succès !";
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            } else {
                $this->message = "❌ Erreur lors de la modification.";
            }
        }

        // Traitement de la suppression
        if (isset($_POST['btn_supprimer'])) {
            $id = $_POST['id'] ?? null;

            if ($id && is_numeric($id)) {
                $this->lapin->id = $id;
                if ($this->lapin->supprimerProduit()) {
                    $_SESSION['message'] = "✅ Lapin supprimé avec succès !";
                } else {
                    $_SESSION['message'] = "❌ Erreur lors de la suppression.";
                }
            } else {
                $_SESSION['message'] = "❌ ID invalide.";
            }

            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        $lapins = $this->lapin->listeProduits();
        include "views/listes/liste_lapins.php";
    }

    public function detailLapin()
    {
        $id    = $_GET['id'] ?? 0;
        $lapin = $this->lapin->getProduitById($id);

        if (!$lapin) {
            $this->message = "❌ Lapin introuvable.";
            header("Location: ?action=liste_lapins");
            exit();
        }

        include "views/detail_lapin.php";
    }

    public function catalog()
    {
        try {
            // URL de base pour les uploads
            $upload_base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/uploads/';

            $terme_recherche = $_GET['search'] ?? '';

            if (!empty($terme_recherche)) {
                $lapins = $this->lapin->rechercherProduits($terme_recherche);
            } else {
                $lapins = $this->lapin->listeProduits();
            }

            // =====================================================
            // CORRECTION PRINCIPALE : une seule fois par produit
            // =====================================================
            $lapins_js = [];
            foreach ($lapins as $lapin) {

                // Construction de l'URL de l'image
                $image_url = !empty($lapin['photo'])
                    ? $upload_base_url . $lapin['photo']
                    : 'https://images.unsplash.com/photo-1518895949257-7621c3c786d7?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80';

                // Compatibilité avec les deux noms de colonnes possibles
                $lapins_js[] = [
                    'id'            => $lapin['id'],
                    'type'          => $lapin['name']        ?? $lapin['nom']      ?? 'Nom non défini',
                    'poids'         => ($lapin['weight_kg']  ?? $lapin['poids_kg'] ?? 0) . 'kg',
                    'prix'          => $lapin['price']       ?? $lapin['prix']     ?? 0,
                    'prixAffichage' => number_format(
                                            $lapin['price'] ?? $lapin['prix'] ?? 0,
                                            0, ',', '.'
                                        ) . ' FCFA',
                    'image'         => $image_url,
                    'description'   => $lapin['description'] ?? 'Description non disponible',
                    'age'           => ($lapin['age_months'] ?? $lapin['age_mois'] ?? 0) . ' mois',
                    'promotion'     => ($lapin['is_featured'] ?? 0) ? 'En vedette' : ''
                ];
            }
            // =====================================================

            // Traitement de l'ajout au panier (POST)
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
                $produit = [
                    'id'          => $_POST['id']          ?? '',
                    'type'        => $_POST['type']        ?? '',
                    'poids'       => $_POST['poids']       ?? '',
                    'prix'        => $_POST['prix']        ?? 0,
                    'image'       => $_POST['image']       ?? '',
                    'description' => $_POST['description'] ?? '',
                    'age'         => $_POST['age']         ?? '',
                    'promotion'   => $_POST['promotion']   ?? ''
                ];

                if (!isset($_SESSION['panier'])) {
                    $_SESSION['panier'] = [];
                }

                $_SESSION['panier'][] = $produit;
                header('Location: clients/panier.php');
                exit();
            }

            include "views/clients/catalog.php";

        } catch (Exception $e) {
            error_log("Erreur dans catalog(): " . $e->getMessage());

            $lapins_js    = [];
            $this->message = "Erreur lors du chargement des produits : " . $e->getMessage();

            include "views/clients/catalog.php";
        }
    }

    public function updateStock()
    {
        if (isset($_POST['rabbit_id']) && isset($_POST['new_stock'])) {
            $rabbit_id = $_POST['rabbit_id'];
            $new_stock = $_POST['new_stock'];

            if ($this->lapin->updateStock($rabbit_id, $new_stock)) {
                echo json_encode(['success' => true,  'message' => 'Stock mis à jour']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        }
    }

    public function Commandes()
    {
        try {
            if (!$this->lapin) {
                throw new Exception("Modèle non initialisé");
            }

            $commandes = $this->lapin->getAllCommandes();

            if (!is_array($commandes)) {
                throw new Exception("Format de données incorrect");
            }

            $title       = "Gestion des Commandes";
            $message     = "";
            $currentPage = 1;
            $totalPages  = 1;

            if (empty($commandes)) {
                $message = "ℹ️ Aucune commande trouvée dans la base de données";
            }

            include "views/Administrateur/commande.php";

        } catch (Exception $e) {
            error_log("Erreur dans Commandes(): " . $e->getMessage());

            $title       = "Erreur";
            $message     = "❌ " . $e->getMessage();
            $commandes   = [];
            $currentPage = 1;
            $totalPages  = 1;

            include "views/Administrateur/commande.php";
        }
    }

    protected function loadView($viewPath, $data = [])
    {
        $viewFile = "views/$viewPath.php";

        if (!file_exists($viewFile)) {
            throw new Exception("La vue $viewFile n'existe pas");
        }

        extract($data);
        require $viewFile;
    }

    // API AJAX
    public function apiGetLapins()
    {
        header('Content-Type: application/json');
        $lapins = $this->lapin->listeProduits();
        echo json_encode($lapins);
    }

    public function apiGetLapin()
    {
        header('Content-Type: application/json');
        $id    = $_GET['id'] ?? 0;
        $lapin = $this->lapin->getProduitById($id);
        echo json_encode($lapin);
    }

    public function facture()
    {
        include "views/facture.php";
    }

    public function paiement()
    {
        include "views/payement.php";
    }

    public function livreur()
    {
        include "views/livreur.html";
    }
}
?>