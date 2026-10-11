<?php
// === Connexion à la base de données ===
include('../../database/connexion2.php');
// === Vérification du token ===
$token = $_GET['token'] ?? '';
$message = '';
$messageType = '';

if (!$token) {
    die("❌ Token manquant");
}

// Cherche le token valide dans la base
$stmt = $con->prepare("SELECT pr.user_id, u.email, pr.expires_at 
                       FROM password_resets pr 
                       JOIN users u ON pr.user_id = u.user_id 
                       WHERE pr.token = :token LIMIT 1");
$stmt->execute([':token' => $token]);
$resetRequest = $stmt->fetch();

if (!$resetRequest) {
    die("❌ Token invalide ou expiré");
}

// Vérifie l'expiration
$now = new DateTime();
$expiresAt = new DateTime($resetRequest['expires_at']);
if ($now > $expiresAt) {
    die("❌ Token expiré. Veuillez refaire une demande de réinitialisation.");
}

// === Traitement du formulaire ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password1 = $_POST['password1'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (empty($password1) || empty($password2)) {
        $message = "⚠️ Tous les champs sont obligatoires";
        $messageType = "warning";
    } elseif ($password1 !== $password2) {
        $message = "⚠️ Les mots de passe ne correspondent pas";
        $messageType = "warning";
    } elseif (strlen($password1) < 6) {
        $message = "⚠️ Le mot de passe doit faire au moins 6 caractères";
        $messageType = "warning";
    } else {
        $hash = password_hash($password1, PASSWORD_DEFAULT);

        $con->prepare("UPDATE users SET password_hash = :pass WHERE user_id = :uid")
            ->execute([':pass' => $hash, ':uid' => $resetRequest['user_id']]);

        $con->prepare("DELETE FROM password_resets WHERE user_id = :uid")
            ->execute([':uid' => $resetRequest['user_id']]);

        $message = "✅ Mot de passe réinitialisé avec succès ! Vous pouvez maintenant vous connecter.";
        $messageType = "success";
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Réinitialisation du mot de passe</title>


<!-- Police Google -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../assets/styles/resset_password.css">
</head>
<body>

<div class="card">
    <h2>🔐 Réinitialiser votre mot de passe</h2>
    <p class="hint">Veuillez saisir un nouveau mot de passe sécurisé</p>

    <?php if ($message): ?>
        <div class="message <?= $messageType ?>"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($messageType !== "success"): ?>
    <form method="post">
        <div class="input-box">
            <input type="password" id="password1" name="password1" placeholder="Nouveau mot de passe" required>
        </div>
        <div class="input-box">
            <input type="password" id="password2" name="password2" placeholder="Confirmez le mot de passe" required>
        </div>

        <label class="show-box">
            <input type="checkbox" onclick="togglePass()" style="width:auto;"> 👁 Voir le mot de passe
        </label>

        <button type="submit">✅ Valider</button>
    </form>
    <a href="../../ClientController/connexion">retour a la page connexion</a>
    <?php endif; ?>
</div>

<script>
function togglePass() {
  let x = document.getElementById("password1");
  let y = document.getElementById("password2");
  let t = x.type === "password" ? "text" : "password";
  x.type = t;
  y.type = t;
}
</script>

</body>
</html>
