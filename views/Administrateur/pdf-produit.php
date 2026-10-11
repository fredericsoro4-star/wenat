<?php
class ProduitController {
    private $con;

    public function __construct() {
        // Connexion PDO intégrée
        $host = 'localhost';
        $dbname = 'u213482981_lapinou_store';
        $username = 'root';
        $password = '';

        try {
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
            die("❌ Erreur de connexion : " . $e->getMessage());
        }
    }

    public function ajouterProduit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Récupération des champs
            $name = trim($_POST['name']);
            $slug = trim($_POST['slug']);
            $description = trim($_POST['description']);
            $breed = trim($_POST['breed']);

            $age_months = isset($_POST['age_months']) && $_POST['age_months'] !== '' ? (int)$_POST['age_months'] : 0;
            $weight_kg = isset($_POST['weight_kg']) && $_POST['weight_kg'] !== '' ? (float)$_POST['weight_kg'] : 0;
            $price = isset($_POST['price']) && $_POST['price'] !== '' ? (float)$_POST['price'] : 0;
            $stock_quantity = isset($_POST['stock_quantity']) && $_POST['stock_quantity'] !== '' ? (int)$_POST['stock_quantity'] : 0;

            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            // Upload image
            $image_url = null;
            if (!empty($_FILES['image_url']['name'])) {
                $upload_dir = __DIR__ . '/uploads/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $file_name = time() . '_' . basename($_FILES['image_url']['name']);
                $target_path = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES['image_url']['tmp_name'], $target_path)) {
                    $image_url = "uploads/" . $file_name;
                }
            }

            try {
                // Insertion en base
                $req = $this->con->prepare('
                    INSERT INTO rabbits 
                    (name, slug, description, breed, age_months, weight_kg, price, stock_quantity, image_url, is_featured, is_active)
                    VALUES 
                    (:name, :slug, :description, :breed, :age_months, :weight_kg, :price, :stock_quantity, :image_url, :is_featured, :is_active)
                ');

                $req->execute([
                    ':name' => $name,
                    ':slug' => $slug,
                    ':description' => $description,
                    ':breed' => $breed,
                    ':age_months' => $age_months,
                    ':weight_kg' => $weight_kg,
                    ':price' => $price,
                    ':stock_quantity' => $stock_quantity,
                    ':image_url' => $image_url,
                    ':is_featured' => $is_featured,
                    ':is_active' => $is_active
                ]);

                // Génération PDF
                require_once __DIR__ . "/../../TCPDF-main/tcpdf.php";
                $pdf = new TCPDF();
                $pdf->AddPage();
                $pdf->SetFont('helvetica', '', 16);
                $pdf->Cell(0, 10, "🐰 FICHE PRODUIT", 0, 1, 'C');
                $pdf->Ln(5);
                $pdf->SetFont('helvetica', '', 14);
                $pdf->Cell(0, 8, "Nom : $name", 0, 1);
                $pdf->Cell(0, 8, "Poids : $weight_kg kg", 0, 1);
                $pdf->Cell(0, 8, "Prix : $price FCFA", 0, 1);

                $pdfFile = __DIR__ . "/uploads/produit_$slug.pdf";
                $pdf->Output($pdfFile, 'F');

                echo "<script>
                        alert('✅ Produit ajouté ! PDF généré.');
                        window.open('uploads/produit_$slug.pdf', '_blank');
                        window.location.href = 'pdf-produit.php';
                      </script>";
                exit;

            } catch (PDOException $e) {
                echo "❌ Erreur SQL : " . $e->getMessage();
                exit;
            }
        }
    }
}

// ========================
// Utilisation
// ========================
$controller = new ProduitController();
$controller->ajouterProduit();
?>
