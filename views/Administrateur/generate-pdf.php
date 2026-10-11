<?php
// generate-pdf.php — aucune sortie avant <?php !

require_once __DIR__ . '/../../TCPDF-main/tcpdf.php';

// 🔥 Inclusion complète pour éviter toutes les erreurs d’Interface & Classes
require_once __DIR__ . '/../../barcode-generator-main/src/Types/TypeInterface.php';
require_once __DIR__ . '/../../barcode-generator-main/src/BarcodeGenerator.php';
require_once __DIR__ . '/../../barcode-generator-main/src/BarcodeGeneratorPNG.php';
require_once __DIR__ . '/../../barcode-generator-main/src/Types/TypeCode128.php';

require __DIR__ . '/../../vendor/autoload.php';


// Exemple rapide pour générer un code-barres
$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
file_put_contents('barcode.png', $generator->getBarcode('1234567890', $generator::TYPE_CODE_128));

// Connexion manuelle (comme tu veux, sans toucher connexion.php)
try {
    $host = 'localhost';
    $dbname = 'u213482981_lapinou_store';
    $username = 'root';
    $password = '';
    $con = new PDO(
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

// Récupérer l’ID de la commande
$commande_id = isset($_GET['commande_id']) ? (int) $_GET['commande_id'] : null;
if(!$commande_id){
    die("Aucune commande sélectionnée.");
}

// Charger les détails de la commande
$stmt = $con->prepare(
    "SELECT d.produit_id, d.quantite, d.prix_unitaire,
            r.name, r.breed, r.age_months
     FROM commande_details d
     LEFT JOIN rabbits r ON d.produit_id = r.id
     WHERE d.commande_id = ?"
);
$stmt->execute([$commande_id]);
$details = $stmt->fetchAll();

// Charger infos client + date commande
$stmt2 = $con->prepare(
    "SELECT u.first_name, u.last_name, u.phone, c.email, c.date_commande
     FROM commandes c
     LEFT JOIN users u ON c.user_id = u.user_id
     WHERE c.id = ?"
);
$stmt2->execute([$commande_id]);
$client = $stmt2->fetch();

// ---------------- CREATION DU PDF STYLE TICKET ----------------
$pdf = new TCPDF('P','mm',[80,250], true, 'UTF-8');
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(4, 4, 4);
$pdf->AddPage();

// En-tête
$pdf->SetFont('dejavusans','B',14);
$pdf->Cell(0,6,"Wenat.shop",0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('dejavusans','',9);
$pdf->Cell(0,4,"DIAMARA MATENIN VOUS A SERVI",0,1,'L');
$pdf->Ln(3);

// Infos client
$nom_client = ($client['first_name'] ?? 'N/A') . " " . ($client['last_name'] ?? '');
$pdf->Cell(0,4,"Client : ".$nom_client,0,1,'L');
$pdf->Cell(0,4,"Téléphone : ".($client['phone'] ?? 'N/A'),0,1,'L');
$pdf->Cell(0,4,"Date : ".($client['date_commande'] ?? date('Y-m-d H:i:s')),0,1,'L');
$pdf->Ln(2);

$pdf->Cell(0,0,'','T');
$pdf->Ln(2);

// Produits
$pdf->SetFont('dejavusans','',8);
$total = 0;
$i = 1;

foreach($details as $item){
    $nom = $item['name'] ?? "Produit";
    $qt = $item['quantite'];
    $pu = $item['prix_unitaire'];
    $st = $pu * $qt;
    $total += $st;

    $pdf->Cell(0,4,$nom,0,1,'L');
    $pdf->Cell(18,4,$qt." x",0,0,'L');
    $pdf->Cell(32,4,number_format($pu,0,',',' ')." CFA",0,0,'R');
    $pdf->Cell(24,4,number_format($st,0,',',' ')." CFA",0,1,'R');
}

$pdf->Ln(2);
$pdf->Cell(0,0,'','T');
$pdf->Ln(2);

// Total
$pdf->SetFont('dejavusans','B',10);
$pdf->Cell(0,6,"TOTAL : ".number_format($total,0,',',' ')." CFA",0,1,'R');
$pdf->Ln(3);

// ESPÈCE section
$pdf->SetFont('dejavusans','',9);
$pdf->Cell(0,5,"ESPECE : ".number_format($total,0,',',' ')." CFA",0,1,'L');
$pdf->Cell(0,5,"RENDUE : 0 CFA",0,1,'L');
$pdf->Ln(3);

$pdf->Cell(0,0,'','T');
$pdf->Ln(3);

// TVA
$pdf->SetFont('dejavusans','',8);
$pdf->Cell(20,4,"TVA",0,0,'L');
$pdf->Cell(20,4,"TAUX",0,0,'L');
$pdf->Cell(20,4,"VAL TVA",0,0,'L');
$pdf->Cell(20,4,"MONTANT HT",0,1,'L');

$pdf->Cell(20,4,"1",0,0,'L');
$pdf->Cell(20,4,"18,00%",0,0,'L');
$pdf->Cell(20,4,"0",0,0,'L');
$pdf->Cell(20,4,number_format($total,0,',',' '),0,1,'L');
$pdf->Ln(3);
$pdf->Cell(0,4,"TOTAL TVA : 0",0,1,'L');

// ---------------- CODE BARRES ----------------
$pdf->Ln(6);
$barcode_text = "CMD".$commande_id;

// 🔥 Utiliser la vraie constante TYPE_CODE_128 de la classe
$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
$barcode_png = $generator->getBarcode($barcode_text, Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128);

// Enregistrer temporairement
$tmp_file = __DIR__ . "/barcode_$commande_id.png";
file_put_contents($tmp_file, $barcode_png);

// Insérer dans le PDF
$pdf->Image($tmp_file, 10, $pdf->GetY(), 60, 15);
@unlink($tmp_file);

$pdf->Ln(16);
$pdf->SetFont('dejavusans','',8);
$pdf->Cell(0,5,"Code : ".$barcode_text,0,1,'C');

$pdf->Ln(2);
$pdf->Cell(0,5,"Merci pour votre visite",0,1,'C');

// Sortie PDF
$pdf->Output('facture_commande_'.$commande_id.'.pdf','I');
exit;
