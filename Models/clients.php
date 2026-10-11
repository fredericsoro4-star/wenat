<?php
class clients
{
    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $numero;
    public $password;
    public $role;
    public $token;

    public $con;

    public function __construct()
    {
        require "database/connexion.php";
        @include 'vendor/autoload.php';
    }

    public function Ajouterclients()
{
    $this->token = sprintf('%06d', random_int(0, 999999));
    $this->password = password_hash($this->password, PASSWORD_DEFAULT);

    $req = $this->con->prepare('
        INSERT INTO users
            (first_name, last_name, email, address, phone, password_hash, role, created_at)
        VALUES
            (:prenom, :nom, :email, :adress, :numero, :password, :role, NOW())
    ');

    $req->bindParam(':nom', $this->nom);
    $req->bindParam(':prenom', $this->prenom);
    $req->bindParam(':email', $this->email);
    $req->bindParam(':adress', $this->adress);
    $req->bindParam(':numero', $this->numero);
    $req->bindParam(':password', $this->password);
    $req->bindParam(':role', $this->role);

    try {
        $success = $req->execute();

        if ($success) {
            try {
                $this->envoyerEmailToken();
            } catch (\Exception $e) {
                error_log("Erreur envoi email : " . $e->getMessage());
            }
        }

        return $success;

    } catch (\PDOException $e) {
        // ✅ Doublon email ou numéro → retourne false proprement
        if ($e->getCode() == 23000) { // code erreur UNIQUE constraint
            return false;
        }
        // Autre erreur inattendue → on la log
        error_log("Erreur BDD : " . $e->getMessage());
        return false;
    }
}

    private function envoyerEmailToken()
    {
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("PHPMailer n'est pas installé");
            return false;
        }

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Configuration SMTP (à personnaliser)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Votre serveur SMTP
            $mail->SMTPAuth   = true;
            $mail->Username   = 'fredericuchiwa52@gmail.com';  // Votre email SMTP
            $mail->Password   = 'sueckonypoquzcil';  // Votre mot de passe
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Destinataires
            $mail->setFrom('noreply@perlesrares.com', 'Perles Rares');
            $mail->addAddress($this->email, $this->prenom . ' ' . $this->nom);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = 'Votre code de verification Perles Rares';
            
            $mail->Body = "
                <h1>Bienvenue chez Perles Rares!</h1>
                <p>Bonjour {$this->prenom},</p>
                <p>Votre compte a été créé avec succès.</p>
                <p>Voici votre code de vérification :</p>
                <h2 style='color: #d4af37;'>{$this->token}</h2>
                <p>Ce code est valable 15 minutes.</p>
                <p>Cordialement,<br>L'équipe Perles Rares</p>
            ";
            
            $mail->AltBody = "Bonjour {$this->prenom},\n\nVotre code de verification est : {$this->token}\n\nCordialement,\nL'équipe Perles Rares";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur d'envoi d'email: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function connexion()
{
    try {
        $req = $this->con->prepare('
            SELECT * FROM users
            WHERE email = :email OR phone = :email
            LIMIT 1
        ');
        
        $req->bindParam(':email', $this->email);
        $req->execute();
        
        $user = $req->fetch(PDO::FETCH_OBJ);
        
        if ($user) {
            if (password_verify($this->password, $user->password_hash)) {
                return $user;
            } else {
                error_log("Mot de passe incorrect pour l'utilisateur: " . $this->email);
            }
        } else {
            error_log("Aucun utilisateur trouvé avec cet email/téléphone: " . $this->email);
        }
    } catch (PDOException $e) {
        error_log("Erreur de base de données lors de la connexion: " . $e->getMessage());
    }
    
    return false;
}




    public function produits()
    {
        $sql = "SELECT * FROM rabbits WHERE is_active = 1";
        $stmt = $this->con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function valider($token)
    {
        $req = $this->con->prepare("
            SELECT user_id as id FROM users
            WHERE role = 'customer'
            LIMIT 1
        ");
        $req->bindParam(':token', $token);
        $req->execute();
        
        if ($req->rowCount() > 0) {
            $userId = $req->fetch(PDO::FETCH_COLUMN);
            
            // Mettre à jour le token (considéré comme validé)
            $update = $this->con->prepare("
                UPDATE users
                SET updated_at = NOW()
                WHERE user_id = :id
            ");
            $update->bindParam(':id', $userId);
            return $update->execute();
        }
        
        return false;
    }
    










    // Ajouter un produit au panier
    public function ajouterAuPanier($produit_id, $quantite = 1) {
        // Vérifier si le produit est déjà dans le panier
        $check = $this->con->prepare("
            SELECT id, quantity FROM panier
            WHERE utilisateur_id = :utilisateur_id AND produit_id = :produit_id
        ");
        $check->bindParam(':utilisateur_id', $this->id);
        $check->bindParam(':produit_id', $produit_id);
        $check->execute();
        
        if ($check->rowCount() > 0) {
            // Mettre à jour la quantité
            $panier = $check->fetch(PDO::FETCH_ASSOC);
            $nouvelleQuantite = $panier['quantite'] + $quantite;
            
            $update = $this->con->prepare("
                UPDATE panier SET quantite = :quantite
                WHERE id = :id
            ");
            $update->bindParam(':quantite', $nouvelleQuantite);
            $update->bindParam(':id', $panier['id']);
            return $update->execute();
        } else {
            // Ajouter nouveau produit
            $insert = $this->con->prepare("
                INSERT INTO panier (utilisateur_id, produit_id, quantite)
                VALUES (:utilisateur_id, :produit_id, :quantite)
            ");
            $insert->bindParam(':utilisateur_id', $this->id);
            $insert->bindParam(':produit_id', $produit_id);
            $insert->bindParam(':quantite', $quantite);
            return $insert->execute();
        }
    }

    // Récupérer le contenu du panier
    public function getPanier() {
        $req = $this->con->prepare("
            SELECT p.*, pa.quantite, (p.prix * pa.quantite) as sous_total
            FROM panier pa
            JOIN produits p ON p.id = pa.produit_id
            WHERE pa.utilisateur_id = :utilisateur_id
        ");
        $req->bindParam(':utilisateur_id', $this->id);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprimer un produit du panier
    public function supprimerDuPanier($produit_id) {
        $req = $this->con->prepare("
            DELETE FROM panier
            WHERE utilisateur_id = :utilisateur_id AND produit_id = :produit_id
        ");
        $req->bindParam(':utilisateur_id', $this->id);
        $req->bindParam(':produit_id', $produit_id);
        return $req->execute();
    }

    // Calculer le total du panier
    public function totalPanier() {
        $req = $this->con->prepare("
            SELECT SUM(p.prix * pa.quantite) as total
            FROM panier pa
            JOIN produits p ON p.id = pa.produit_id
            WHERE pa.utilisateur_id = :utilisateur_id
        ");
        $req->bindParam(':utilisateur_id', $this->id);
        $req->execute();
        return $req->fetch(PDO::FETCH_COLUMN);
    }
    






    
    // Créer une commande à partir du panier
    public function creerCommande($adresse_livraison) {
        try {
            $this->con->beginTransaction();
            
            // Récupérer le total du panier
            $total = $this->totalPanier();
            if (!$total) {
                throw new Exception("Le panier est vide");
            }
            
            // Créer la commande
            $insertCmd = $this->con->prepare("
                INSERT INTO commandes (utilisateur_id, montant_total, adresse_livraison)
                VALUES (:utilisateur_id, :montant_total, :adresse_livraison)
            ");
            $insertCmd->bindParam(':utilisateur_id', $this->id);
            $insertCmd->bindParam(':montant_total', $total);
            $insertCmd->bindParam(':adresse_livraison', $adresse_livraison);
            $insertCmd->execute();
            
            $commande_id = $this->con->lastInsertId();
            
            // Récupérer les produits du panier
            $panier = $this->getPanier();
            
            // Ajouter les détails de la commande
            foreach ($panier as $item) {
                $insertDetail = $this->con->prepare("
                    INSERT INTO details_commande (commande_id, produit_id, quantite, prix_unitaire)
                    VALUES (:commande_id, :produit_id, :quantite, :prix_unitaire)
                ");
                $insertDetail->bindParam(':commande_id', $commande_id);
                $insertDetail->bindParam(':produit_id', $item['id']);
                $insertDetail->bindParam(':quantite', $item['quantite']);
                $insertDetail->bindParam(':prix_unitaire', $item['prix']);
                $insertDetail->execute();
                
                // Mettre à jour le stock
                $updateStock = $this->con->prepare("
                    UPDATE produits
                    SET stock = stock - :quantite
                    WHERE id = :produit_id
                ");
                $updateStock->bindParam(':quantite', $item['quantite']);
                $updateStock->bindParam(':produit_id', $item['id']);
                $updateStock->execute();
            }
            
            // Vider le panier
            $viderPanier = $this->con->prepare("
                DELETE FROM panier
                WHERE utilisateur_id = :utilisateur_id
            ");
            $viderPanier->bindParam(':utilisateur_id', $this->id);
            $viderPanier->execute();
            
            $this->con->commit();
            return $commande_id;
        } catch (Exception $e) {
            $this->con->rollback();
            error_log("Erreur lors de la création de la commande: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer les commandes d'un client
    public function getCommandes() {
        $req = $this->con->prepare("
            SELECT * FROM commandes
            WHERE utilisateur_id = :utilisateur_id
            ORDER BY date_commande DESC
        ");
        $req->bindParam(':utilisateur_id', $this->id);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les détails d'une commande
    public function getDetailsCommande($commande_id) {
        $req = $this->con->prepare("
            SELECT dc.*, p.nom, p.photo
            FROM details_commande dc
            JOIN produits p ON p.id = dc.produit_id
            WHERE dc.commande_id = :commande_id
        ");
        $req->bindParam(':commande_id', $commande_id);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}