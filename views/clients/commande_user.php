<?php
session_start();

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

class Database {
    public $con;
    public function __construct() {
        require "../../database/connexion.php";
    }
}

class CommandeManager {
    private $db;
    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getCommandesParUtilisateur($user_id) {
        $sql = "SELECT 
                    c.id,
                    c.date_commande,
                    c.validation,
                    c.statut,
                    c.montant_total
                FROM commandes c
                WHERE c.user_id = ?
                ORDER BY c.date_commande DESC";

        $stmt = $this->db->con->prepare($sql);
        $stmt->execute([$user_id]);
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($commandes as &$commande) {
            $commande['details'] = $this->getDetailsCommande($commande['id']);
        }

        return $commandes;
    }

    private function getDetailsCommande($commande_id) {
        $sql = "SELECT 
                    cd.quantite,
                    cd.prix_unitaire,
                    (cd.quantite * cd.prix_unitaire) AS sous_total,
                    r.name AS produit_nom
                FROM commande_details cd
                LEFT JOIN rabbits r ON cd.produit_id = r.id
                WHERE cd.commande_id = ?";

        $stmt = $this->db->con->prepare($sql);
        $stmt->execute([$commande_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$db = new Database();
$commandeManager = new CommandeManager($db);

$user_id = $_SESSION['user']['id'];
$commandes = $commandeManager->getCommandesParUtilisateur($user_id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mes commandes</title>

<link rel="stylesheet" href="../../assets/styles/commande-user.css">

</head>

<body>
<div class="container">
<h2>📦 Mes commandes</h2>

<?php if($commandes): ?>
    <?php foreach($commandes as $commande): ?>
    <div class="card">
        <div class="header">
            <div class="date">
                📅 <?= date("d/m/Y H:i", strtotime($commande['date_commande'])) ?>
            </div>
            <div class="header-right">
                <div class="total">
                    <?= number_format($commande['montant_total'],0,","," ") ?> FCFA
                </div>
                <?php
                $validation = $commande['validation'];
                if($validation == "validé"){
                    echo "<span class='badge valide'>✔ Validé</span>";
                }
                elseif($validation == "refusé"){
                    echo "<span class='badge refuse'>✖ Refusé</span>";
                }
                else{
                    echo "<span class='badge attente'>⏳ En attente</span>";
                }
                ?>
            </div>
        </div>

        <?php foreach($commande['details'] as $produit): ?>
        <div class="produit">
            <div>
                <b><?= htmlspecialchars($produit['produit_nom']) ?></b><br>
                <small>
                    <?= $produit['quantite'] ?> × <?= number_format($produit['prix_unitaire'],0,","," ") ?> FCFA
                </small>
            </div>
            <div class="prix">
                <?= number_format($produit['sous_total'],0,","," ") ?> FCFA
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

<?php else: ?>
<div class="empty">
<h3>📭 Aucune commande trouvée</h3>
<p>Vous n'avez encore passé aucune commande.</p>
</div>
<?php endif; ?>

</div>
</body>
</html>