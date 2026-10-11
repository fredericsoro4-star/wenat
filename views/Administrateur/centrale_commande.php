<?php
// test-commandes.php
// Place ce fichier à la racine et accède-y directement.
// Version robuste + debug + style amélioré

declare(strict_types=1);
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

$host = 'localhost';
$dbname = 'u213482981_lapinou_store';
$username = 'root';
$password = '';

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

try {
    $con = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // --- Détecter la colonne clé dans la table users (user_id ou id)
    $userIdColumn = null;
    try {
        $stmt = $con->query("SHOW COLUMNS FROM `users` LIKE 'user_id'");
        if ($stmt && $stmt->fetch()) {
            $userIdColumn = 'user_id';
        } else {
            $stmt = $con->query("SHOW COLUMNS FROM `users` LIKE 'id'");
            if ($stmt && $stmt->fetch()) {
                $userIdColumn = 'id';
            }
        }
    } catch (PDOException $ex) {
        // si la table users n'existe pas, userIdColumn restera null
        $userIdColumn = null;
    }

    // --- Compter les commandes (sécurisé)
    $countStmt = $con->query("SELECT COUNT(*) AS c FROM commandes");
    $countRow = $countStmt->fetch();
    $totalCommandes = $countRow ? (int)$countRow['c'] : 0;

    // --- Récupérer la liste d'utilisateurs qui ont des commandes
    // On construira la jointure en fonction du nom de colonne trouvé
    if ($userIdColumn !== null) {
        $joinCondition = "LEFT JOIN users u ON c.user_id = u.`{$userIdColumn}`";
        $selectUserCols = "u.first_name, u.last_name, u.email, u.{$userIdColumn} AS user_id_from_users";
    } else {
        // fallback: récupère juste user_id depuis commandes si users introuvable
        $joinCondition = "";
        $selectUserCols = "NULL AS first_name, NULL AS last_name, NULL AS email, c.user_id AS user_id_from_users";
    }

    $usersQuery = "
        SELECT DISTINCT
            c.user_id,
            {$selectUserCols},
            COUNT(c.id) as nb_commandes
        FROM commandes c
        {$joinCondition}
        GROUP BY c.user_id
        ORDER BY nb_commandes DESC
    ";
    $users = $con->query($usersQuery)->fetchAll();

} catch (PDOException $e) {
    // Sortie HTML minimale en cas d'erreur de connexion
    echo "<!doctype html><html><head><meta charset='utf-8'><title>Erreur</title></head><body>";
    echo "<h2 style='color:red;'>❌ Erreur de base de données</h2>";
    echo "<pre>" . e($e->getMessage()) . "</pre>";
    echo "</body></html>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Lapinou Store — Test Commandes</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    :root{
        --accent:#4CAF50;
        --bg:#f0f2f5;
        --card:#ffffff;
        --muted:#666;
    }
    *{box-sizing:border-box}
    body {
        font-family: 'Roboto', sans-serif;
        background: var(--bg);
        margin: 0;
        padding: 20px;
        color:#222;
    }
    .container {
        max-width:1100px;
        margin: 24px auto;
        background: transparent;
        padding: 0 16px;
    }
    header {
        text-align:center;
        margin-bottom:16px;
    }
    h1 { margin:6px 0 4px; font-size:1.8rem; }
    .sub { color:var(--muted); margin-bottom:18px; }

    .summary {
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        margin-bottom:14px;
    }
    .summary .box {
        background:var(--card);
        padding:14px 18px;
        border-radius:10px;
        box-shadow:0 6px 18px rgba(0,0,0,0.06);
    }
    .users-list { margin-top:10px; }

    .user-card {
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        background:var(--card);
        padding:12px 16px;
        border-radius:8px;
        box-shadow:0 6px 12px rgba(0,0,0,0.04);
        margin-bottom:10px;
    }
    .user-left { display:flex; gap:12px; align-items:center; }
    .avatar {
        width:46px; height:46px; border-radius:8px;
        background:linear-gradient(135deg,#e8f5e9,#c8e6c9);
        display:flex; align-items:center; justify-content:center;
        font-weight:700; color:#256029;
    }
    .user-meta small { color:var(--muted); display:block; }
    .btn {
        background:var(--accent);
        color:#fff;
        padding:8px 12px;
        text-decoration:none;
        border-radius:6px;
        font-weight:700;
    }
    .btn:active{transform:translateY(1px)}

    table {
        width:100%;
        border-collapse:collapse;
        margin-top:18px;
        background:var(--card);
        box-shadow:0 6px 12px rgba(0,0,0,0.04);
        border-radius:8px;
        overflow:hidden;
    }
    th, td { padding:12px 14px; text-align:left; }
    th { background:var(--accent); color:#fff; font-weight:700; }
    tr:nth-child(even) td { background:#fbfdfb; }

    .status {
        display:inline-block;
        padding:6px 10px;
        border-radius:8px;
        color:#fff;
        font-weight:700;
    }
    .status.completed{background:#2e7d32}
    .status.pending{background:#ef6c00}
    .status.cancelled{background:#c62828}
    .commande-card {
        background:var(--card);
        padding:16px;
        margin:14px 0;
        border-left:5px solid var(--accent);
        border-radius:8px;
        box-shadow:0 6px 12px rgba(0,0,0,0.04);
    }

    footer { margin-top:28px; color:var(--muted); font-size:0.9rem; text-align:center; }
    .small-note { font-size:0.9rem; color:var(--muted); margin-top:8px }
    @media (max-width:720px){
        .summary{flex-direction:column; align-items:stretch}
        .user-card{flex-direction:column; align-items:flex-start}
    }
</style>
</head>
<body>
<div class="container">
    <header>
        <h1>🧪 Test des Commandes — Lapinou Store</h1>
        <div class="sub">Interface de contrôle et debug des commandes</div>
    </header>

    <div class="summary">
        <div class="box"><strong>📦 Total commandes :</strong> <?= e($totalCommandes) ?></div>
        <div class="box small-note">Base de données: <?= e($dbname) ?> — Host: <?= e($host) ?></div>
    </div>

    <section>
        <h2>👥 Utilisateurs avec commandes</h2>
        <div class="users-list">
            <?php if (empty($users)): ?>
                <div class="user-card"><div>Aucun utilisateur trouvé.</div></div>
            <?php else: ?>
                <?php foreach ($users as $user): 
                    // le user_id provient de la colonne 'user_id' dans commandes
                    $user_id = isset($user['user_id']) ? $user['user_id'] : ($user['user_id_from_users'] ?? null);
                    $firstname = $user['first_name'] ?? '';
                    $lastname = $user['last_name'] ?? '';
                    $email = $user['email'] ?? '';
                    $nb = $user['nb_commandes'] ?? 0;
                ?>
                    <div class="user-card">
                        <div class="user-left">
                            <div class="avatar"><?= e(strtoupper(substr(($firstname ?: 'U'),0,1))) ?></div>
                            <div class="user-meta">
                                <strong><?= e(($firstname || $lastname) ? trim($firstname.' '.$lastname) : "Utilisateur #".$user_id) ?></strong>
                                <small><?= e($email ?: '— email non disponible —') ?></small>
                                <small>Commandes: <?= e($nb) ?></small>
                            </div>
                        </div>
                        <div>
                            <a class="btn" href="?test_user=<?= e((int)$user_id) ?>">🔍 Voir commandes</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

<?php
// --- Affichage des commandes d'un utilisateur si demandé
if (isset($_GET['test_user'])):
    $test_user_id = (int)$_GET['test_user'];

    // Requête paramétrée et sûre
    $query = "
        SELECT 
            c.id AS commande_id,
            c.date_commande,
            c.montant_total,
            c.statut,
            cd.quantite,
            cd.prix_unitaire,
            (cd.quantite * cd.prix_unitaire) AS sous_total,
            r.name AS produit_nom
        FROM commandes c
        INNER JOIN commande_details cd ON c.id = cd.commande_id
        LEFT JOIN rabbits r ON cd.produit_id = r.id
        WHERE c.user_id = :uid
        ORDER BY c.date_commande DESC, c.id DESC
    ";
    $stmt = $con->prepare($query);
    $stmt->execute([':uid' => $test_user_id]);
    $results = $stmt->fetchAll();

    echo "<section>";
    echo "<h2>📋 Commandes de l'utilisateur #".e($test_user_id)."</h2>";
    if (!$results) {
        echo "<div class='commande-card'><strong>❌ Aucun résultat trouvé pour cet utilisateur.</strong></div>";
    } else {
        // Tableau récapitulatif
        echo "<table aria-label='Liste des produits commandés'>";
        echo "<thead><tr><th>Commande ID</th><th>Date</th><th>Produit</th><th>Quantité</th><th>Prix Unit.</th><th>Sous-total</th><th>Statut</th></tr></thead><tbody>";
        foreach ($results as $row) {
            $statut_raw = $row['statut'] ?? 'unknown';
            // normaliser statut en classes utilisables
            $statut_class = strtolower(preg_replace('/[^a-z0-9]+/i','-', $statut_raw));
            // limiter la classe à quelques valeurs connues
            if (!in_array($statut_class, ['completed','pending','cancelled'])) {
                // map commun
                if (strpos($statut_class,'encours') !== false || strpos($statut_class,'pending') !== false) $statut_class = 'pending';
                elseif (strpos($statut_class,'cancel') !== false || strpos($statut_class,'annul') !== false) $statut_class = 'cancelled';
                elseif (strpos($statut_class,'complete') !== false || strpos($statut_class,'paid') !== false) $statut_class = 'completed';
                else $statut_class = 'pending';
            }
            echo "<tr>";
            echo "<td>#".e($row['commande_id'])."</td>";
            echo "<td>".e(date('d/m/Y H:i', strtotime($row['date_commande'] ?? 'now')))."</td>";
            echo "<td>".e($row['produit_nom'] ?? '—')."</td>";
            echo "<td>".e($row['quantite'] ?? 0)."</td>";
            echo "<td>".e(($row['prix_unitaire'] ?? 0))." FCFA</td>";
            echo "<td><strong>".e(($row['sous_total'] ?? 0))." FCFA</strong></td>";
            echo "<td><span class='status {$statut_class}'>".e($row['statut'] ?? '—')."</span></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";

        // Vue groupée par commande
        $grouped = [];
        foreach ($results as $r) {
            $cid = $r['commande_id'];
            if (!isset($grouped[$cid])) {
                $grouped[$cid] = [
                    'info' => [
                        'commande_id' => $cid,
                        'date_commande' => $r['date_commande'],
                        'montant_total' => $r['montant_total'] ?? null,
                        'statut' => $r['statut'] ?? null,
                    ],
                    'details' => []
                ];
            }
            $grouped[$cid]['details'][] = $r;
        }

        echo "<hr>";
        echo "<h3>📦 Vue groupée par commande</h3>";
        foreach ($grouped as $g) {
            $info = $g['info'];
            echo "<div class='commande-card'>";
            echo "<h4>Commande #".e($info['commande_id'])." — ".e($info['montant_total'] ?? 'N/A')." FCFA</h4>";
            echo "<p><em>Date: ".e(date('d/m/Y H:i', strtotime($info['date_commande'] ?? 'now')))."</em></p>";
            echo "<ul>";
            foreach ($g['details'] as $d) {
                echo "<li>".e($d['produit_nom'] ?? '—')." x".e($d['quantite'])." = ".e($d['sous_total'])." FCFA</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
    }
    echo "</section>";
endif;
?>

    <footer>
        <div>Fichier de test &mdash; Lapinou Store</div>
        <div class="small-note">Si tu vois encore une erreur, copie/colle ici le message d'erreur exact ou une capture — je corrige directement.</div>
    </footer>
</div>
</body>
</html>
