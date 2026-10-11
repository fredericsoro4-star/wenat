<?php
include('../../database/connexion2.php');
// === Inclusion PHPMailer ===
require __DIR__ . '/../../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$messageType = ''; // success, error, warning

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // Vérifie si l'utilisateur existe
    $stmt = $con->prepare('SELECT user_id, email FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $userId = $user['user_id'];
        $token = bin2hex(random_bytes(32));
        $expiresAt = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

        $con->prepare('DELETE FROM password_resets WHERE user_id = :uid')->execute([':uid' => $userId]);
        $con->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid, :token, :exp)')
            ->execute([':uid' => $userId, ':token' => $token, ':exp' => $expiresAt]);

       $resetLink = 'https://wenat.shop/views/clients/reset_password.php?token=' . $token;


        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'fredericsoro4@gmail.com';
            $mail->Password   = 'ixsmjzeicsrpmrfo';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('fredericsoro4@gmail.com', 'Lapinou Store');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = '🔐 Réinitialisation de votre mot de passe';
            $mail->Body = "
                <p>Bonjour,</p>
                <p>Voici votre lien pour réinitialiser votre mot de passe (valide 1 heure) :</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>Si vous n'avez pas demandé cela, ignorez cet e-mail.</p>
            ";

            $mail->send();
            $message = " Email envoyé avec succès à <strong>$email</strong>";
            $messageType = 'success';

        } catch (Exception $e) {
            $message = "❌ Erreur lors de l'envoi : " . htmlspecialchars($mail->ErrorInfo);
            $messageType = 'error';
        }
    } else {
        $message = "⚠️ Aucun utilisateur trouvé avec l'email <strong>$email</strong>";
        $messageType = 'warning';
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Mot de passe oublié</title>
<link rel="stylesheet" href="../../assets/styles/motdepasseoublie.css">

</head>
<body>

<div class="card">
    <img src="../../assets/images/image.png" alt="Logo Lapinou Store">
    <h2>Mot de passe oublié ?</h2>
    <p class="hint">Entrez votre email pour recevoir un lien de réinitialisation</p>

    <form method="post">
        <input type="email" name="email" placeholder="votre@email.com" required>
        <button type="submit">Envoyer le lien</button>
    </form>

    <?php if($message): ?>
        <div class="message <?= $messageType ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>
    <a href="../../ClientController/connexion" class="retour">retour a connexion</a>

</div>

</body>
</html>
