<?php
session_start();

if (!isset($_SESSION['user']['id'])) {
    header('Location: ../../ClientController/connexion');
    exit();
}

include('../../database/connexion2.php');

$user_id = $_SESSION['user']['id'];

$stmt = $con->prepare("SELECT first_name, last_name, email, phone, address, position FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $address    = $_POST['address'];
    $password   = $_POST['password'];
    $position   = $_POST['position'];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone=?, address=?, password_hash=?, position=? WHERE user_id=?");
        $stmt->execute([$first_name, $last_name, $email, $phone, $address, $password_hash, $position, $user_id]);
    } else {
        $stmt = $con->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone=?, address=?, position=? WHERE user_id=?");
        $stmt->execute([$first_name, $last_name, $email, $phone, $address, $position, $user_id]);
    }

    $message = "Modifications enregistrées avec succès !";

    $stmt = $con->prepare("SELECT first_name, last_name, email, phone, address, position FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

$initials  = strtoupper(mb_substr($user['first_name'], 0, 1) . mb_substr($user['last_name'], 0, 1));
$full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
$email_val = htmlspecialchars($user['email'] ?? '');
$phone_val = htmlspecialchars($user['phone'] ?? '');
$addr_val  = htmlspecialchars($user['address'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Paramètres — Mon Compte</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../assets/styles/paramettre.css">
</head>
<body>

<div class="page">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <div class="flip-container" id="flipCard" onclick="toggleFlip()">
            <div class="flipper">

                <!-- FACE AVANT -->
                <div class="card-front">
                    <div class="badge-verified">
                        <i class="fas fa-shield-alt"></i> Vérifié
                    </div>
                    <div class="front-body">
                        <div class="avatar"><?= $initials ?></div>
                        <div class="front-info">
                            <div class="user-name"><?= $full_name ?></div>
                            <div class="user-email"><?= $email_val ?></div>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="stats">
                        <div class="stat">
                            <div class="stat-val">12</div>
                            <div class="stat-lbl">Commandes</div>
                        </div>
                        <div class="stat">
                            <div class="stat-val">3</div>
                            <div class="stat-lbl">En cours</div>
                        </div>
                    </div>
                </div>

                <!-- FACE ARRIÈRE — QR CODE -->
                <div class="card-back">
                    <div class="qr-label">Ma carte de contact</div>
                    <div id="qrcode"></div>
                    <div class="qr-hint">Scannez pour mes coordonnées</div>
                </div>

            </div>
        </div>

        <div class="flip-hint">
            <i class="fas fa-sync-alt" style="font-size:10px"></i>
            Cliquez pour retourner la carte
        </div>

    </div>

    <!-- CONTENU -->
    <div class="content">

        <?php if ($message): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <!-- INFOS PERSONNELLES -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-user"></i></div>
                    <span class="card-title">Informations personnelles</span>
                </div>

                <div class="grid-2">
                    <div class="field">
                        <label>Prénom</label>
                        <div class="input-wrap">
                            <i class="fas fa-user"></i>
                            <input type="text" name="first_name"
                                value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="field">
                        <label>Nom</label>
                        <div class="input-wrap">
                            <i class="fas fa-user"></i>
                            <input type="text" name="last_name"
                                value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                        </div>
                    </div>
                </div>

                <div style="height:14px"></div>

                <div class="field">
                    <label>Email</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email"
                            value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                </div>

                <div style="height:14px"></div>

                <div class="field">
                    <label>Téléphone</label>
                    <div class="input-wrap">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="phone"
                            value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                </div>

                <div style="height:14px"></div>

                <div class="field">
                    <label>Adresse</label>
                    <div class="input-wrap">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" name="address"
                            value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- SECURITE -->
            <div class="card" style="margin-top:16px">
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-lock"></i></div>
                    <span class="card-title">Sécurité</span>
                </div>
                <div class="field">
                    <label>Nouveau mot de passe</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password"
                            placeholder="Laisser vide pour ne pas changer">
                    </div>
                </div>
            </div>

            <input type="hidden" name="position" id="position"
                value="<?= htmlspecialchars($user['position'] ?? '') ?>">

            <!-- GPS -->
            <div class="gps-card" style="margin-top:16px">
                <div class="gps-left">
                    <div class="gps-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="gps-text">
                        <strong>Ma position GPS</strong>
                        <span>Cliquez pour enregistrer votre localisation</span>
                    </div>
                </div>
                <button type="button" class="btn-gps" onclick="getLocation()">
                    <i class="fas fa-location-crosshairs"></i>
                    Localiser
                </button>
            </div>

            <!-- SAVE -->
            <div style="margin-top:16px">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    Enregistrer les modifications
                </button>
            </div>

        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>

var qrContent = [
    "BEGIN:VCARD",
    "VERSION:3.0",
    "FN:<?= addslashes($full_name) ?>",
    "EMAIL:<?= addslashes($email_val) ?>",
    "TEL:<?= addslashes($phone_val) ?>",
    "ADR:;;<?= addslashes($addr_val) ?>;;;",
    "END:VCARD"
].join("\n");

var qrGenerated = false;

function generateQR() {
    if (qrGenerated) return;
    qrGenerated = true;
    new QRCode(document.getElementById("qrcode"), {
        text: qrContent,
        width: 110,
        height: 110,
        colorDark: "#2e1065",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
    });
}

var flipped = false;

function toggleFlip() {
    flipped = !flipped;
    var card = document.getElementById('flipCard');
    if (flipped) {
        card.classList.add('flipped');
        setTimeout(generateQR, 400);
    } else {
        card.classList.remove('flipped');
    }
}

function getLocation() {
    if (!navigator.geolocation) {
        alert("La géolocalisation n'est pas supportée par votre navigateur.");
        return;
    }
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            var lat = pos.coords.latitude.toFixed(6);
            var lon = pos.coords.longitude.toFixed(6);
            document.getElementById('position').value = lat + ',' + lon;
            alert('Position enregistrée : ' + lat + ', ' + lon);
        },
        function() {
            alert("Impossible d'obtenir votre position.");
        }
    );
}
</script>

</body>
</html>