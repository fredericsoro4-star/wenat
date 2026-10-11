<?php
class Produits
{
    public $id;
    public $nom;
    public $prix;
    public $description;
    public $categorie;
    public $stock;
    public $photo;
    public $race;
    public $age_mois;
    public $poids_kg;
    public $slug;
    public $is_featured;
    public $is_active;
    private $db;
    public $con;

    public function __construct()
    {
        require_once "database/connexion.php";
    }









    
  private function slugExists($slug)
    {
        try {
            $req = $this->con->prepare('SELECT id FROM rabbits WHERE slug = :slug LIMIT 1');
            $req->bindParam(':slug', $slug);
            $req->execute();
            return $req->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur slugExists(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ajoute un nouveau produit
     */
    public function ajouterProduit()
    {
        try {
            $this->slug = $this->generateSlug($this->nom);
            
            $req = $this->con->prepare('
                INSERT INTO rabbits 
                    (name, slug, description, breed, age_months, weight_kg, price, stock_quantity, image_url, is_featured, is_active)
                VALUES 
                    (:nom, :slug, :description, :race, :age_mois, :poids_kg, :prix, :stock, :photo, :is_featured, :is_active)
            ');

            $req->bindParam(':nom', $this->nom);
            $req->bindParam(':slug', $this->slug);
            $req->bindParam(':description', $this->description);
            $req->bindParam(':race', $this->race);
            $req->bindParam(':age_mois', $this->age_mois, PDO::PARAM_INT);
            $req->bindParam(':poids_kg', $this->poids_kg);
            $req->bindParam(':prix', $this->prix);
            $req->bindParam(':stock', $this->stock, PDO::PARAM_INT);
            $req->bindParam(':photo', $this->photo);
            $req->bindParam(':is_featured', $this->is_featured, PDO::PARAM_INT);
            $req->bindParam(':is_active', $this->is_active, PDO::PARAM_INT);

            $result = $req->execute();
            
            if (!$result) {
                error_log("Erreur execute: " . print_r($req->errorInfo(), true));
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Erreur ajouterProduit(): " . $e->getMessage());
            throw $e;
        }
}


































 public function listeProduits()
    {
        try {
            $sql = "SELECT 
                    id,
                    name as nom,
                    slug,
                    price as prix,
                    description,
                    breed as race,
                    age_months as age_mois,
                    weight_kg as poids_kg,
                    stock_quantity as stock,
                    image_url as photo,
                    is_featured,
                    is_active,
                    created_at as date_ajout
                    FROM rabbits
                    WHERE is_active = 1
                    ORDER BY created_at DESC";
            
            $query = $this->con->prepare($sql);
            $query->execute();
            
            $resultats = $query->fetchAll(PDO::FETCH_ASSOC);
            error_log("Nombre de lapins trouvés : " . count($resultats));
            
            return $resultats;
            
        } catch (PDOException $e) {
            error_log("Erreur listeProduits(): " . $e->getMessage());
            return [];
        }
}

    public function getProduitById($id)
    {
        try {
            $sql = "SELECT * FROM rabbits WHERE rabbit_id = :id AND is_active = 1";
            $query = $this->con->prepare($sql);
            $query->bindParam(':id', $id);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getProduitById(): " . $e->getMessage());
            return false;
        }
    }

    public function modifierProduit()
    {
        try {
            $sql = "UPDATE rabbits SET 
                    name = :nom,
                    description = :description,
                    breed = :race,
                    age_months = :age_mois,
                    weight_kg = :poids_kg,
                    price = :prix,
                    stock_quantity = :stock,
                    image_url = :photo,
                    is_featured = :is_featured,
                    is_active = :is_active
                    WHERE rabbit_id = :id";
            
            $stmt = $this->con->prepare($sql);
            
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':race', $this->race);
            $stmt->bindParam(':age_mois', $this->age_mois);
            $stmt->bindParam(':poids_kg', $this->poids_kg);
            $stmt->bindParam(':prix', $this->prix);
            $stmt->bindParam(':stock', $this->stock);
            $stmt->bindParam(':photo', $this->photo);
            $stmt->bindParam(':is_featured', $this->is_featured);
            $stmt->bindParam(':is_active', $this->is_active);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Erreur modifierProduit(): " . $e->getMessage());
            return false;
        }
    }
public function supprimerProduit()
{
    if (!isset($this->id)) {
        error_log("Erreur supprimerProduit(): ID non défini");
        return false;
    }

    try {
        $query = "DELETE FROM rabbits WHERE id = :id";
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0; // true si un produit a été supprimé
    } catch (PDOException $e) {
        error_log("Erreur supprimerProduit(): " . $e->getMessage());
        return false;
    }
}


  public function rechercherProduits($terme)
{
    try {
        $sql = "SELECT 
                id,
                name as nom,
                slug,
                price as prix,
                description,
                breed as race,
                age_months as age_mois,
                weight_kg as poids_kg,
                stock_quantity as stock,
                image_url as photo,       -- ← alias ajouté
                is_featured,
                is_active,
                created_at as date_ajout
                FROM rabbits 
                WHERE is_active = 1 
                AND (name LIKE :terme 
                     OR description LIKE :terme 
                     OR breed LIKE :terme)
                ORDER BY created_at DESC";
        
        $query = $this->con->prepare($sql);
        $terme = '%' . $terme . '%';
        $query->bindParam(':terme', $terme);
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur rechercherProduits(): " . $e->getMessage());
        return [];
    }
}
    public function getProduitsEnVedette()
    {
        try {
            $sql = "SELECT * FROM rabbits 
                    WHERE is_active = 1 AND is_featured = 1 
                    ORDER BY created_at DESC 
                    LIMIT 6";
            
            $query = $this->con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getProduitsEnVedette(): " . $e->getMessage());
            return [];
        }
    }

    public function updateStock($rabbit_id, $nouvelle_quantite)
    {
        try {
            $sql = "UPDATE rabbits SET stock_quantity = :stock WHERE rabbit_id = :id";
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':stock', $nouvelle_quantite);
            $stmt->bindParam(':id', $rabbit_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur updateStock(): " . $e->getMessage());
            return false;
        }
    }


    
    private function generateSlug($nom)
    {
        // Convertir en minuscules
        $slug = strtolower($nom);
        
        // Remplacer les caractères accentués
        $slug = str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ'],
            ['a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'd', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'th', 'y'],
            $slug
        );
        
        // Remplacer les espaces et caractères spéciaux par des tirets
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        // Supprimer les tirets en début/fin
        $slug = trim($slug, '-');
        
        return $slug;
    }



    //compte des totaux 
    public function getStatistiques()
    {
        try {
            $stats = [];
            
            // Total des lapins
            $sql = "SELECT COUNT(*) as total FROM rabbits WHERE is_active = 1";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $stats['total_lapins'] = $stmt->fetch()['total'];
            
            // Lapins en stock
            $sql = "SELECT COUNT(*) as en_stock FROM rabbits WHERE is_active = 1 AND stock_quantity > 0";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $stats['lapins_en_stock'] = $stmt->fetch()['en_stock'];
            
            // Lapins en rupture
            $sql = "SELECT COUNT(*) as rupture FROM rabbits WHERE is_active = 1 AND stock_quantity = 0";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $stats['lapins_rupture'] = $stmt->fetch()['rupture'];
            
            // Prix moyen
            $sql = "SELECT AVG(price) as prix_moyen FROM rabbits WHERE is_active = 1";
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $stats['prix_moyen'] = $stmt->fetch()['prix_moyen'];
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Erreur getStatistiques(): " . $e->getMessage());
            return [];
        }
    }

//partie de voir les commande de lutilisateur

public function getAllCommandes() {
        try {
            $query = "SELECT 
    c.id AS commande_id,
    c.user_id,
    c.first_name,
    c.last_name,
    c.email,
    c.phone,
    c.date_commande,
    c.montant_total,
    c.statut,
    cd.id AS detail_id,
    cd.produit_id,
    cd.quantite,
    cd.prix_unitaire,
    (cd.quantite * cd.prix_unitaire) AS sous_total,
    r.name AS produit_nom,
    r.image_url AS produit_image,
    r.price AS prix_courant,
    r.sku AS reference_sku
FROM commandes c
INNER JOIN commande_details cd 
    ON c.id = cd.commande_id
LEFT JOIN rabbits r 
    ON cd.produit_id = r.rabbit_id
ORDER BY c.date_commande DESC
LIMIT 0, 25;
";

            $stmt = $this->con->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debug logs
            error_log("Résultats bruts: " . count($results));
            if (!empty($results)) {
                error_log("Première ligne: " . json_encode($results[0]));
            }

            // Restructurer les données pour correspondre au format attendu par la vue
            $commandes = [];
            
            foreach ($results as $row) {
                $commandeId = $row['commande_id'];
                
                // Si cette commande n'existe pas encore dans notre tableau
                if (!isset($commandes[$commandeId])) {
                    $commandes[$commandeId] = [
                        'id' => $commandeId,
                        'user_id' => $row['user_id'],
                        'date_commande' => $row['date_commande'],
                        'montant_total' => $row['montant_total'],
                        'statut' => $row['statut'],
                        'client' => [
                            'first_name' => $row['first_name'],
                            'last_name' => $row['last_name'],
                            'email' => $row['email'],
                            'phone' => $row['phone']
                        ],
                        'produits' => []
                    ];
                }
                
                // Ajouter le produit à cette commande
                $commandes[$commandeId]['produits'][] = [
                    'produit_id' => $row['produit_id'],
                    'nom' => $row['produit_nom'] ?: 'Produit #' . $row['produit_id'],
                    'quantite' => $row['quantite'],
                    'prix_unitaire' => $row['prix_unitaire'],
                    'sous_total' => $row['sous_total'],
                    'image_url' => $row['produit_image'],
                    'prix_courant' => $row['prix_courant'],
                    'reference' => $row['reference_sku']
                ];
            }

            // Convertir en tableau indexé numériquement
            $commandesFormatted = array_values($commandes);
            
            error_log("Commandes formatées: " . count($commandesFormatted));
            if (!empty($commandesFormatted)) {
                error_log("Première commande formatée: " . json_encode($commandesFormatted[0]));
            }

            return $commandesFormatted;

        } catch (PDOException $e) {
            error_log("Erreur PDO dans getAllCommandes: " . $e->getMessage());
            return [];
        }
    }




public function getRabbitById($rabbitId) 
    {
        // Validation de l'ID
        if (!is_numeric($rabbitId) || $rabbitId <= 0) {
            return null;
        }

        // Préparation de la requête
        $query = "SELECT * FROM rabbits WHERE rabbit_id = :rabbit_id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':rabbit_id', $rabbitId, PDO::PARAM_INT);
        
        // Exécution
        $stmt->execute();
        
        // Récupération du résultat
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

// Dans votre RabbitModel.php
public function getRabbitByProductId($product_id) {
    try {
        $query = "SELECT r.* FROM rabbits r
                 JOIN produits p ON r.rabbit_id = p.rabbit_id
                 WHERE p.produit_id = :product_id";
        
        $stmt = $this->con->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getRabbitByProductId(): " . $e->getMessage());
        return null;
    }
}
}
?>