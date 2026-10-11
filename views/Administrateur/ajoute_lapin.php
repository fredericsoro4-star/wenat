<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$host     = 'localhost';
$dbname   = 'u213482981_lapinou_store';
$username = 'root';
$password = '';

try {
    $con = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username, $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name           = trim($_POST['name']);
    $slug           = trim($_POST['slug']);
    $description    = trim($_POST['description']);
    $breed          = trim($_POST['breed']);
    $age_months     = !empty($_POST['age_months'])     ? (int)$_POST['age_months']     : 0;
    $weight_kg      = !empty($_POST['weight_kg'])      ? (float)$_POST['weight_kg']    : 0;
    $price          = !empty($_POST['price'])          ? (float)$_POST['price']        : 0;
    $stock_quantity = !empty($_POST['stock_quantity']) ? (int)$_POST['stock_quantity'] : 0;
    $is_featured    = isset($_POST['is_featured'])     ? 1 : 0;
    $is_active      = isset($_POST['is_active'])       ? 1 : 0;

    $upload_dir = __DIR__ . "/../../uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $image_url = null;
    if (!empty($_FILES['image_url']['name'])) {
        $file_name   = time() . '_' . basename($_FILES['image_url']['name']);
        $target_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['image_url']['tmp_name'], $target_path)) {
            $image_url = $file_name;
        }
    }

    $stmt = $con->prepare("
        INSERT INTO rabbits (name, slug, description, breed, age_months, weight_kg, price, stock_quantity, image_url, is_featured, is_active)
        VALUES (:name, :slug, :description, :breed, :age_months, :weight_kg, :price, :stock_quantity, :image_url, :is_featured, :is_active)
    ");
    $stmt->execute([
        ':name' => $name, ':slug' => $slug, ':description' => $description,
        ':breed' => $breed, ':age_months' => $age_months, ':weight_kg' => $weight_kg,
        ':price' => $price, ':stock_quantity' => $stock_quantity,
        ':image_url' => $image_url, ':is_featured' => $is_featured, ':is_active' => $is_active,
    ]);
    $product_id = $con->lastInsertId();

    require_once __DIR__ . "/../../TCPDF-main/tcpdf.php";

    // ─── Paramètres de mise en page ───────────────────────────────────────────
    $W   = 110;   // largeur page mm
    $mL  = 2;     // marge gauche mm — poussé à gauche
    $mR  = 14;    // marge droite mm
    $iW  = $W - $mL - $mR;  // largeur utile = 94 mm
    $sep = 4;     // espace entre blocs mm
    $lH  = 4.5;   // interligne description mm
    $hBC = 18;    // hauteur code-barre mm

    // ─── PHASE 1 : PDF fantôme pour mesurer la hauteur réelle du contenu ─────
    $g = new TCPDF('P', 'mm', [$W, 500]);
    $g->setPrintHeader(false);
    $g->setPrintFooter(false);
    $g->SetMargins($mL, 0, $mR);
    $g->SetAutoPageBreak(false);
    $g->AddPage();
    $g->SetY(0);

    $g->SetFont('helvetica', 'B', 11);
    $g->Cell($iW, 8, strtoupper($name), 0, 1, 'C');
    $g->Ln($sep);

    $g->SetFont('helvetica', '', 7.5);
    $g->Cell($iW, 5, "Poids : {$weight_kg} kg   |   Age : {$age_months} mois   |   Type : {$breed}", 0, 1, 'C');
    $g->Ln($sep);

    $g->SetFont('helvetica', '', 7.5);
    $g->MultiCell($iW, $lH, $description, 0, 'C', false, 1);
    $g->Ln($sep);

    $g->Ln($hBC);  // code-barre = hauteur fixe
    $g->Ln($sep);

    $g->SetFont('helvetica', 'B', 7.5);
    $g->Cell($iW, 5, 'REF', 0, 1, 'C');
    $g->Ln($sep);

    $g->SetFont('helvetica', 'I', 7);
    $g->Cell($iW, 5, 'wenat.shop', 0, 1, 'C');

    $contentH = $g->GetY();  // hauteur réelle mesurée par TCPDF

    // ─── PHASE 2 : taille de page et startY pour centrage vertical ───────────
    $pageH  = max(100, $contentH + 20);      // marge haut+bas = 10mm chacun minimum
    $startY = ($pageH - $contentH) / 2;      // centrage vertical parfait

    // ─── PHASE 3 : vrai PDF avec dimensions exactes ───────────────────────────
    $pdf = new TCPDF('P', 'mm', [$W, $pageH]);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins($mL, $startY, $mR);
    $pdf->SetAutoPageBreak(false);
    $pdf->AddPage();
    $pdf->SetY($startY);  // forcer le curseur au bon point de départ

    // NOM
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell($iW, 8, strtoupper($name), 0, 1, 'C');
    $pdf->Ln($sep);

    // INFOS
    $pdf->SetFont('helvetica', '', 7.5);
    $pdf->Cell($iW, 5,
        "Poids : {$weight_kg} g   |   Type : {$breed}",
        0, 1, 'C');
    $pdf->Ln($sep);

    // DESCRIPTION — s'étend automatiquement selon le texte
    $pdf->SetFont('helvetica', '', 7.5);
    $pdf->MultiCell($iW, $lH, $description, 0, 'C', false, 1);
    $pdf->Ln($sep);

    // CODE-BARRE — centré, exactement après la description
    $bc_y = $pdf->GetY();
    $bc_w = round($iW * 0.80);            // 80% de la largeur utile
    $bc_x = $mL + ($iW - $bc_w) / 2 + 20;  // décalé de 24mm vers la droite
    $pdf->write1DBarcode(
        (string)$product_id, 'C128',
        $bc_x, $bc_y, $bc_w, $hBC, 0.5,
        ['text' => false]
    );

    // RÉFÉRENCE & PRIX
    $pdf->SetXY($mL, $bc_y + $hBC + $sep);
    $pdf->SetFont('helvetica', 'B', 7.5);
    $pdf->Cell($iW, 5,
        "REF : " . str_pad($product_id, 6, '0', STR_PAD_LEFT)
        . "   |   " . number_format($price, 0, ',', ' ') . " FCFA",
        0, 1, 'C');
    $pdf->Ln($sep);

    // SITE
    $pdf->SetFont('helvetica', 'I', 7);
    $pdf->Cell($iW, 5, "wenat.shop", 0, 1, 'C');

    while (ob_get_level() > 0) ob_end_clean();
    $pdf->Output("etiquette_{$slug}.pdf", "I");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .card { border: none; }
        .card-header { background: linear-gradient(135deg, #28a745, #20c997); }
    </style>
</head>
<body class="p-4">
<div class="container" style="max-width: 720px;">
    <div class="card shadow-lg rounded-4 overflow-hidden">
        <div class="card-header py-3">
            <h2 class="text-white text-center mb-0 fw-bold"> Ajouter un Produit</h2>
        </div>
        <div class="card-body p-4">
            <form method="POST" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nom du produit</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Slug</label>
                        <input type="text" class="form-control" name="slug" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea class="form-control" name="description" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Type / Race</label>
                    <input type="text" class="form-control" name="breed" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Âge (mois)</label>
                        <input type="number" class="form-control" name="age_months" min="0" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Poids (kg)</label>
                        <input type="number" step="0.01" class="form-control" name="weight_kg" min="0" value="0">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Prix (FCFA)</label>
                        <input type="number" step="0.01" class="form-control" name="price" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Quantité en stock</label>
                        <input type="number" class="form-control" name="stock_quantity" value="0" min="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Image du produit</label>
                    <input type="file" class="form-control" name="image_url" accept="image/*">
                </div>

                <div class="d-flex gap-4 mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_featured" value="1" id="chk_featured">
                        <label class="form-check-label" for="chk_featured">⭐ Mettre en avant</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" id="chk_active" checked>
                        <label class="form-check-label" for="chk_active">✅ Actif</label>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success px-5 py-2 fw-bold fs-5">
                        💾 Enregistrer &amp; Générer l'étiquette
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
</body>
</html>