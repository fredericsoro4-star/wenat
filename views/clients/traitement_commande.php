<?php
header('Content-Type: application/json');

// ─── PHPMailer ────────────────────────────────────────────────────────────────
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../PHPMailer-master/src/SMTP.php';

// ─── Méthode ──────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// ─── Lecture JSON ─────────────────────────────────────────────────────────────
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// ─── Vérification utilisateur connecté ───────────────────────────────────────
if (!isset($data['user_id']) || $data['user_id'] === null || $data['user_id'] === 'null') {
    echo json_encode(['success' => false, 'message' => 'Désolé, créez un compte ou connectez-vous']);
    exit;
}

// ─── Connexion BDD ────────────────────────────────────────────────────────────
include('../../database/connexion2.php');

$commandeId   = null;
$email_envoye = false;

try {
    // ── Variables ──────────────────────────────────────────────────────────────
    $user_id       = $data['user_id'];
    $first_name    = $data['first_name'] ?? null;
    $last_name     = $data['last_name']  ?? null;
    $email         = $data['email']      ?? null;
    $phone         = $data['phone']      ?? null;
    $date_commande = date('Y-m-d H:i:s');
    $montant_total = $data['total'];
    $statut        = 'en attente';

    // ── Vérification utilisateur en BDD ───────────────────────────────────────
    $stmtCheck = $con->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmtCheck->execute([$user_id]);

    if (!$stmtCheck->fetch()) {
        throw new Exception("L'utilisateur spécifié n'existe pas dans la base de données");
    }

    // ── Insertion commande ────────────────────────────────────────────────────
    $stmt = $con->prepare("INSERT INTO commandes 
                          (user_id, first_name, last_name, email, phone, date_commande, montant_total, statut) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $first_name, $last_name, $email, $phone, $date_commande, $montant_total, $statut]);

    $commandeId = $con->lastInsertId();

    // ── Insertion détails commande ────────────────────────────────────────────
    $stmtDetail = $con->prepare("INSERT INTO commande_details 
                                (commande_id, produit_id, quantite, prix_unitaire) 
                                VALUES (?, ?, ?, ?)");
    foreach ($data['items'] as $item) {
        $stmtDetail->execute([$commandeId, $item['id'], $item['quantity'], $item['price']]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ENVOI EMAIL
    // ─────────────────────────────────────────────────────────────────────────

    $prenom        = htmlspecialchars($first_name);
    $nom           = htmlspecialchars($last_name);
    $date_lisible  = date('d/m/Y à H:i');
    $annee         = date('Y');
    $total_formate = number_format($montant_total, 2, ',', ' ');

    // ── Lignes produits pour l'email ──────────────────────────────────────────
    $lignes_produits = '';
    foreach ($data['items'] as $item) {
        $sous_total      = number_format($item['price'] * $item['quantity'], 2, ',', ' ');
        $nom_produit     = htmlspecialchars($item['name']);
        $img             = htmlspecialchars($item['image']);
        $qte             = (int)$item['quantity'];
        $lignes_produits .= "
        <tr>
            <td style='padding:12px 10px; border-bottom:1px solid #c8e6c9; vertical-align:middle;'>
                <img src='{$img}' width='50' height='50'
                     style='border-radius:8px; object-fit:cover; vertical-align:middle; margin-right:10px;'>
                <span style='vertical-align:middle;'>{$nom_produit}</span>
            </td>
            <td style='padding:12px 10px; border-bottom:1px solid #c8e6c9; text-align:center; vertical-align:middle;'>
                x{$qte}
            </td>
            <td style='padding:12px 10px; border-bottom:1px solid #c8e6c9; text-align:right; vertical-align:middle; font-weight:600;'>
                {$sous_total} FCFA
            </td>
        </tr>";
    }

    // ── Corps HTML de l'email ─────────────────────────────────────────────────
    $email_body = "
    <div style='font-family:Arial,sans-serif; max-width:600px; margin:auto; background:#fff;
                border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08);'>

        <!-- HEADER -->
        <div style='background:linear-gradient(135deg, #1b5e20, #43a047); padding:40px 30px; text-align:center;'>
            <h1 style='color:#ffffff; margin:0; font-size:28px;'>&#x1F430; Wenat Shop</h1>
            <p style='color:#c8e6c9; margin:8px 0 0; font-size:14px;'>Votre boutique de confiance</p>
        </div>

        <!-- CORPS -->
        <div style='padding:35px 30px;'>
            <h2 style='color:#2e7d32; margin-top:0;'>Merci {$prenom} !</h2>
            <p style='color:#555; line-height:1.7; font-size:15px;'>
                Votre commande <strong style='color:#2e7d32;'>#CMD-{$commandeId}</strong>
                pass&eacute;e le <strong>{$date_lisible}</strong>
                a bien &eacute;t&eacute; re&ccedil;ue et est en cours de traitement.<br><br>
                Nous vous contacterons tr&egrave;s prochainement pour confirmer la livraison.
            </p>

            <h3 style='color:#2e7d32; border-bottom:2px solid #c8e6c9; padding-bottom:10px; margin-top:30px;'>
                R&eacute;capitulatif de votre commande
            </h3>

            <table style='width:100%; border-collapse:collapse; font-size:14px;'>
                <thead>
                    <tr style='background:#2e7d32;'>
                        <th style='padding:12px 10px; text-align:left;   color:#ffffff;'>Produit</th>
                        <th style='padding:12px 10px; text-align:center; color:#ffffff;'>Qt&eacute;</th>
                        <th style='padding:12px 10px; text-align:right;  color:#ffffff;'>Prix</th>
                    </tr>
                </thead>
                <tbody>{$lignes_produits}</tbody>
            </table>

            <!-- TOTAL -->
            <div style='background:#e8f5e9; border-left:4px solid #2e7d32; border-radius:8px;
                        padding:15px 20px; margin-top:20px; text-align:right;'>
                <span style='font-size:20px; font-weight:bold; color:#2e7d32;'>
                    Total : {$total_formate} FCFA
                </span>
            </div>

            <p style='color:#555; margin-top:30px; line-height:1.7; font-size:15px;'>
                Pour toute question, r&eacute;pondez directement &agrave; cet email.<br>
                Merci de nous faire confiance !
            </p>
        </div>

        <!-- FOOTER -->
        <div style='background:#e8f5e9; padding:20px 30px; text-align:center;
                    border-top:2px solid #c8e6c9;'>
            <p style='color:#2e7d32; font-size:13px; margin:0; font-weight:600;'>
                &#x1F430; Wenat Shop
            </p>
            <p style='color:#888; font-size:12px; margin:6px 0 0;'>
                &copy; {$annee} Wenat Shop &mdash; Tous droits r&eacute;serv&eacute;s
            </p>
        </div>

    </div>";

    // ── Envoi avec PHPMailer ──────────────────────────────────────────────────
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sorosongui352@gmail.com';
    $mail->Password   = 'nlng pqsu dabn ruqk';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('sorosongui352@gmail.com', 'Wenat Shop');
    $mail->addAddress($email, $prenom . ' ' . $nom);

    $mail->isHTML(true);
    $mail->Subject = "Commande #CMD-{$commandeId} confirmee - Wenat Shop";
    $mail->Body    = $email_body;
    $mail->AltBody = "Bonjour {$prenom}, votre commande #CMD-{$commandeId} a bien ete recue. Total : {$total_formate} FCFA. Merci ! Wenat Shop";

    $mail->send();
    $email_envoye = true;

} catch (PDOException $e) {
    // Erreur BDD → bloquant
    echo json_encode(['success' => false, 'message' => 'Erreur BDD : ' . $e->getMessage()]);
    exit;

} catch (Exception $e) {
    if ($commandeId) {
        // Commande OK mais email échoué → on log, on ne bloque pas
        error_log("[Wenat] Email non envoye pour commande #{$commandeId} : " . $e->getMessage());
        $email_envoye = false;
    } else {
        // Erreur avant l'insertion → bloquant
        echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
        exit;
    }
}

// ─── Réponse finale ───────────────────────────────────────────────────────────
echo json_encode([
    'success'      => true,
    'message'      => 'Commande enregistree',
    'commande_id'  => $commandeId,
    'email_envoye' => $email_envoye
]);