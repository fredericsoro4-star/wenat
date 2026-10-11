<?php
require_once 'Models/clients.php'; 

class ClientController {
    public $clients;
    public $message = '';

    public function __construct() {
        // Démarrer la session une seule fois dans le constructeur
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->clients = new Clients();
    }
public function inscription() {

    $this->message = '';

    if (isset($_POST['btn_inscription'])) {

        $this->clients->nom     = $_POST['sai_nom'] ?? '';
        $this->clients->prenom  = $_POST['sai_prenom'] ?? '';
        $this->clients->email   = $_POST['sai_email'] ?? '';
        $this->clients->adress  = $_POST['sai_adress'] ?? '';
        $this->clients->numero  = $_POST['sai_numero'] ?? '';
        $this->clients->password = $_POST['sai_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $this->clients->role    = $_POST['sai_role'] ?? 'clients';

        if (
            empty($this->clients->nom) ||
            empty($this->clients->prenom) ||
            empty($this->clients->email) ||
            empty($this->clients->password)
        ) {

            $this->message = "❌ Tous les champs obligatoires doivent être remplis";

        }
        
        // ✅ Vérifier longueur mot de passe
        elseif (strlen($this->clients->password) < 4) {

            $this->message = "❌ Le mot de passe doit contenir au moins 4 caractères";

        }

        // ✅ Vérifier confirmation mot de passe
        elseif ($this->clients->password !== $confirm_password) {

            $this->message = "❌ Les mots de passe ne correspondent pas";

        }

        else {

            if ($this->clients->Ajouterclients()) {

                $this->clients->email    = $_POST['sai_email'];
                $this->clients->password = $_POST['sai_password'];
                $user = $this->clients->connexion();

                if ($user && is_object($user)) {

                    $_SESSION['user'] = [
                        'id'     => $user->user_id ?? null,
                        'nom'    => $user->last_name ?? '',
                        'prenom' => $user->first_name ?? '',
                        'email'  => $user->email ?? '',
                        'numero' => $user->phone ?? '',
                        'role'   => $user->role ?? 'clients'
                    ];

                    $this->message = "✅ Inscription réussie ! Bienvenue " . $user->first_name;

                    switch ($_SESSION['user']['role']) {
                        case 'admin':
                            $redirect = '../LapinController/dashboard';
                            break;
                        case 'delivery':
                            $redirect = '../LivreurController/dashboard';
                            break;
                        default:
                            $redirect = '../index.php';
                            break;
                    }

                    $this->redirect = $redirect;
                }

            } else {

                $this->message = "❌ Erreur lors de l'inscription. L'email ou le numéro existe peut-être déjà.";

            }
        }
    }

    $message  = $this->message;
    $redirect = $this->redirect ?? null;

    include "views/clients/inscription.php";
}
public function connexion() {

    $this->message = '';

    if (isset($_POST['btn_connexion'])) {

        $email = $_POST['sai_identifiant'] ?? '';
        $password = $_POST['sai_password'] ?? '';

        if (empty($email) || empty($password)) {

            $this->message = "❌ Tous les champs sont obligatoires";

        } else {

            $this->clients->email = $email;
            $this->clients->password = $password;

            $user = $this->clients->connexion();

            if ($user && is_object($user)) {

                $_SESSION['user'] = [
                    'id' => $user->user_id ?? null,
                    'nom' => $user->last_name ?? '',
                    'prenom' => $user->first_name ?? '',
                    'email' => $user->email ?? '',
                    'numero' => $user->phone ?? '',
                    'role' => $user->role ?? 'clients'
                ];

                // message succès
                $this->message = "✅ Connexion réussie ! Bienvenue ".$user->first_name;

                // redirection selon le rôle
                switch ($_SESSION['user']['role']) {

                    case 'admin':
                        $redirect = '../LapinController/dashboard';
                    break;

                    case 'delivery':
                        $redirect = '../LivreurController/Commandes';
                    break;

                    default:
                        $redirect = '../index.php';
                    break;
                }

                echo "<script>
                        setTimeout(function(){
                            window.location.href='$redirect';
                        },3000);
                      </script>";

            } else {

                // ❌ message erreur ici
                $this->message = "❌ Identifiants incorrects ou compte inexistant";

            }
        }
    }

    $message = $this->message;

    include "views/clients/connexion.php";
}
    public function deconnexion() {
        // Détruire toutes les données de session
        $_SESSION = array();
        
        // Effacer également le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Détruire la session
        session_destroy();
        
        // Redirection
        $this->redirect('../ClientController/connexion');
    }

    // Méthode helper pour les redirections
    private function redirect($url) {
        if (ob_get_length()) {
            ob_end_clean();
        }
        header("Location: $url");
        exit();
    }
}