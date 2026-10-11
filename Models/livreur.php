<?php
class LivreurModel {
    private $con;

    public function __construct() {
       
            require "database/connexion.php";
            
          
    }


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
}