<?php
class administrateur
{
    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $numero;
    public $password;
    public $role;


    public $con;

    public function __construct()
    {
        require "database/connexion.php";

    }

    public function Ajouterutilisateur()
    {
       
        $this->token = sprintf('%06d', random_int(0, 999999));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $req = $this->con->prepare('
            INSERT INTO users
                (first_name, last_name, email, phone, password_hash, role, created_at)
            VALUES
                (:prenom, :nom, :email, :numero, :password, :role, NOW())
        ');

        $req->bindParam(':nom', $this->nom);
        $req->bindParam(':prenom', $this->prenom);
        $req->bindParam(':email', $this->email);
        $req->bindParam(':numero', $this->numero);
        $req->bindParam(':password', $this->password);
        $req->bindParam(':role', $this->role);

       $success = $req->execute();

       

       return $success;
    }

    
    public function connexion()
    {
        $req = $this->con->prepare('
            SELECT * FROM users
            WHERE email = :email OR phone = :email
        ');
        
        $req->bindParam(':email', $this->email);
        $req->execute();
        
        $user = $req->fetch(PDO::FETCH_OBJ);
        
        if ($user && password_verify($this->password, $user->password_hash)) {
            return $user;
        }
        
        return false;
       }

public function listeclients()
{
    try {
        $sql = "SELECT 
                user_id as id,
                first_name as prenom,
                last_name as nom,
                email,
                phone as numero,
                role,
                address as adresse,
                is_active,
                password_hash,
                DATE_FORMAT(created_at, '%d/%m/%Y') as date_inscription
                FROM users
                WHERE role = 'clients'
                ORDER BY created_at DESC";

        $query = $this->con->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erreur listeclients(): " . $e->getMessage());
        return [];
    }
}




public function modifierutilisateur()
{
    try {
        $sql = "UPDATE users SET
                first_name = :prenom,
                last_name = :nom,
                email = :email,
                phone = :numero,
                role = :role
                WHERE user_id = :id";
        
        $query = $this->con->prepare($sql);
        return $query->execute([
            ':id' => $this->id,
            ':prenom' => $this->prenom,
            ':nom' => $this->nom,
            ':email' => $this->email,
            ':numero' => $this->numero,
            ':role' => $this->role
        ]);
    } catch (PDOException $e) {
        error_log("Erreur modifierClient(): " . $e->getMessage());
        return false;
    }
}





public function supprimerutilisateurs()
{
    try {
        $sql = "DELETE FROM users WHERE user_id = :id";
        $query = $this->con->prepare($sql);
        return $query->execute([':id' => $this->id]);
    } catch (PDOException $e) {
        error_log("Erreur supprimerutilisateurs(): " . $e->getMessage());
        return false;
    }
}



public function listelivreurs()
{
    try {
        $sql = "SELECT 
                user_id as id,
                first_name as prenom,
                last_name as nom,
                email,
                phone as numero,
                role,
                is_active,
                DATE_FORMAT(created_at, '%d/%m/%Y') as date_inscription
                FROM users
                WHERE role = 'delivery'
                ORDER BY created_at DESC";
        
        $query = $this->con->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur listelivreur(): " . $e->getMessage());
        return [];
    }
}

  }