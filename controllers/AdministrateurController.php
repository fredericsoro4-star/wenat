<?php
require_once 'Models/administrateur.php'; 

class AdministrateurController{
    public $clients;
    public $livreur;
    public $message = '';

    public function __construct() {
        $this->clients = new administrateur();
        $this->livreur = new administrateur(); // Même modèle mais pour les livreurs
    }


    public function inscription()
    {
        if (isset($_POST['btn_inscription'])) {
            $this->clients->nom = $_POST['sai_nom'] ?? '';
            $this->clients->prenom = $_POST['sai_prenom'] ?? '';
            $this->clients->email = $_POST['sai_email'] ?? '';
            $this->clients->numero = $_POST['sai_numero'] ?? '';
            $this->clients->password = $_POST['sai_password'] ?? '';
            $this->clients->role = $_POST['sai_role'] ?? 'clients';

            if (empty($this->clients->nom) || empty($this->clients->prenom) ||
               empty($this->clients->email) || empty($this->clients->password)) {
                $this->message = "❌ Tous les champs obligatoires doivent être remplis";
            } else {
                if ($this->clients->Ajouterutilisateur()) {
                    $this->message = "✅ Inscription réussie ! Vous pouvez maintenant vous connecter.";
                    // Rediriger vers la page de connexion après inscription
                    header('Location: ?action=connexion&type=login&success=1');
                    exit();
                } else {
                    $this->message = "❌ Erreur lors de l'inscription. L'email ou le numéro existe peut-être déjà.";
                }
            }
        }
        
        include "views/Administrateur/inscription_utilisteur.php";
    }



    public function connexion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->message = '';

        // Message de succès après inscription
        if (isset($_GET['success']) && $_GET['success'] == '1') {
            $this->message = "✅ Inscription réussie ! Vous pouvez maintenant vous connecter.";
        }

        if (isset($_POST['btn_connexion'])) {
           
            $email = $_POST['sai_email'] ?? '';
            $password = $_POST['sai_password'] ?? '';

            if (empty($email) || empty($password)) {
                $this->message = "❌ Tous les champs sont obligatoires";
            } else {
                $this->clients->email = $email;
                $this->clients->password = $password;

                $user = $this->clients->Connexion();
                if ($user) {
                    $_SESSION['user'] = [
                        'id' => $user->user_id,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name
                    ];

                    if ($user->role === 'admin') {
                        header('Location: ?action=dashboard');
                    } else {
                        header('Location: /');
                    }
                    exit();
                } else {
                    $this->message = "❌ Identifiants incorrects ou compte inexistant";
                }
            }
        }

        $message = $this->message;
        include "views/Administrateur/inscription_utilisteur.php";
    }
    



public function listeclients()
{
    // Traitement de la modification
    if (isset($_POST['btn_modifier'])) {
        $this->clients->id = $_POST['id'] ?? null;
         $this->clients->prenom = $_POST['prenom'] ?? '';
        $this->clients->nom = $_POST['nom'] ?? '';
        $this->clients->email = $_POST['email'] ?? '';
        $this->clients->numero = $_POST['numero'] ?? '';
        
        $this->clients->role = $_POST['role'] ?? 'clients';
     
        if ($this->clients->modifierutilisateur()) {
            $this->message = "✅ Client modifié avec succès";
        } else {
            $this->message = "❌ Erreur lors de la modification";
        }
    }

    // Traitement de la suppression
    if (isset($_POST['btn_supprimer'])) {
        $this->clients->id = $_POST['id'] ?? null;
        
        if ($this->clients->supprimerutilisateurs()) {
            $this->message = "✅ Client supprimé avec succès";
        } else {
            $this->message = "❌ Erreur lors de la suppression";
        }
    }

    // Récupération de tous les clients
    $clients = $this->clients->listeclients();
    
    include "views/listes/liste_clients.php";
}






 public function listelivreurs() {
        // Traitement de la modification
         // Traitement de la modification
    if (isset($_POST['btn_modifier'])) {
        $this->clients->id = $_POST['id'] ?? null;
        $this->clients->prenom = $_POST['prenom'] ?? '';
        $this->clients->nom = $_POST['nom'] ?? '';
        $this->clients->email = $_POST['email'] ?? '';
        $this->clients->numero = $_POST['numero'] ?? '';
        $this->clients->role = $_POST['role'] ?? 'clients';

        // ✅ Si un mot de passe est envoyé, on le re-hashe
        if (!empty($_POST['password'])) {
            $this->clients->password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        }
     
        if ($this->clients->modifierutilisateur()) {
            $this->message = "✅ Client modifié avec succès";
        } else {
            $this->message = "❌ Erreur lors de la modification";
        }
    }

        // Traitement de la suppression
        if (isset($_POST['btn_supprimer'])) {
            $this->livreur->id = $_POST['id'] ?? null;
            
            if ($this->livreur->supprimerutilisateurs()) { // Changé de clients à livreur
                $this->message = "✅ Livreur supprimé avec succès"; // Message corrigé
            } else {
                $this->message = "❌ Erreur lors de la suppression";
            }
        }

        // Récupération de tous les livreurs
        $livreurs = $this->livreur->listelivreurs(); // Changé de livreurs à livreur
        
        include "views/listes/liste_livreur.php";
    }









   

    public function deconnexion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Détruire toutes les données de session
        session_unset();
        session_destroy();
        
        // Rediriger vers la page d'accueil
        header('Location: /');
        exit();
    }
}
?>