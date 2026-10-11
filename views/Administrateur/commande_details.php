<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

// =======================
// 🔗 CLASSE DE CONNEXION
// =======================
class Database {
    public $con;

    public function __construct() {
        try {
            $host = 'localhost';
            $dbname = 'u213482981_lapinou_store';
            $username = 'root';
            $password = '';
            $this->con = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    // Récupération des détails de la commande
    public function getCommandeDetails($commande_id) {
        $sql = "SELECT cd.produit_id, cd.quantite, cd.prix_unitaire, 
                       r.name, r.breed, r.age_months, r.description, r.image_url
                FROM commande_details cd
                JOIN rabbits r ON cd.produit_id = r.id
                WHERE cd.commande_id = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([$commande_id]);
        return $stmt->fetchAll();
    }
}

// =======================
// 📦 RÉCUPÉRATION DES DÉTAILS
// =======================
if (!isset($_GET['commande_id'])) {
    die("Aucune commande spécifiée !");
}

$commande_id = $_GET['commande_id'];
$db = new Database();
$details = $db->getCommandeDetails($commande_id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Facture - Commande #<?= htmlspecialchars($commande_id) ?></title>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
    h1 { text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #f4f4f4; }
    .total { font-weight: bold; text-align: right; }
    .center { text-align: center; }
    .back { margin-top: 20px; display: inline-block; padding: 10px 20px; background: #4CAF50; color: #fff; text-decoration: none; border-radius: 5px; }
</style>
</head>
<body>

<h1>Facture - Commande #<?= htmlspecialchars($commande_id) ?></h1>

<?php if (count($details) > 0): ?>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Produit</th>
            <th>Race</th>
            <th>Âge (mois)</th>
            <th>Quantité</th>
            <th>Prix unitaire (FCFA)</th>
            <th>Sous-total (FCFA)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total = 0;
        $i = 1;
        foreach ($details as $item):
            $sousTotal = $item['prix_unitaire'] * $item['quantite'];
            $total += $sousTotal;
        ?>
        <tr>
            <td class="center"><?= $i++ ?></td>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= htmlspecialchars($item['breed']) ?></td>
            <td class="center"><?= htmlspecialchars($item['age_months']) ?></td>
            <td class="center"><?= htmlspecialchars($item['quantite']) ?></td>
            <td class="center"><?= number_format($item['prix_unitaire'],0,',',' ') ?></td>
            <td class="center"><?= number_format($sousTotal,0,',',' ') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<a href="generate-pdf.php?commande_id=<?= $commande_id ?>" class="btn">
   🧾 Télécharger la facture PDF
</a>

<style>
.btn{
    background:#000;
    color:#fff;
    padding:5px 5px;
    text-decoration:none;
    border-radius:5px;
}
</style>

<h2 class="total">Montant total : <?= number_format($total,0,',',' ') ?> FCFA</h2>

<a href="../../LapinController/Commandes" class="back">Retour aux commandes</a>

<?php else: ?>
<p>Aucun produit trouvé pour cette commande.</p>
<?php endif; ?>

</body>
</html>
