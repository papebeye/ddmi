<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Config\Database;
use App\Libraries\Auth;

/**
 * Contrôleur pour la gestion de l'authentification
 */
class LoginController extends BaseController {
    /**
     * Connexion à la base de données
     * 
     * @var \mysqli
     */
    private $db;
    
    /**
     * Instance d'authentification
     * 
     * @var Auth
     */
    private $auth;
    
    /**
     * URL de base pour les redirections
     * 
     * @var string
     */
    private $baseUrl;
    
    /**
     * Constructeur
     */
    public function __construct() {
        // Établir la connexion à la base de données
        $dbConfig = new Database();
        $this->db = $dbConfig->connect();
        
        // Initialiser l'authentification
        $this->auth = new Auth($this->db);
        
        // Si la session n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Définir l'URL de base pour les redirections
        $this->baseUrl = '/ddmi/src/public';
    }
    
    /**
     * Affiche le formulaire de connexion
     */
    public function index() {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if ($this->auth->isLoggedIn()) {
            $this->redirect('dashboard');
            return;
        }
        
        // Charger les messages flash
        $flashMessages = $this->getFlashMessages();
        
        // Inclure la vue
        include dirname(dirname(__DIR__)) . '/Views/auth/login.php';
    }
    
    /**
     * Traite la tentative de connexion
     */
    public function authenticate() {
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
            return;
        }
        
        // Récupérer les données du formulaire
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Valider les entrées
        if (empty($username) || empty($password)) {
            $this->setFlashMessage('error', 'Veuillez remplir tous les champs.');
            $this->redirect('login');
            return;
        }
        
        // Tenter de connecter l'utilisateur
        $success = $this->auth->login($username, $password, $remember);
        
        if ($success) {
            // Connexion réussie
            $this->setFlashMessage('success', 'Connexion réussie.');
            
            // Différencier le tableau de bord selon le rôle
            if ($this->auth->isAdmin() || $this->auth->isCentralRole()) {
                $this->redirect('admin/dashboard');
            } else {
                $this->redirect('dashboard');
            }
        } else {
            // Échec de la connexion
            $this->setFlashMessage('error', 'Nom d\'utilisateur ou mot de passe incorrect.');
            $this->redirect('login');
        }
    }
    
    /**
     * Déconnecte l'utilisateur
     */
    public function logout() {
        $this->auth->logout();
        $this->setFlashMessage('success', 'Vous avez été déconnecté avec succès.');
        $this->redirect('login');
    }
    
    /**
     * Méthode pour définir un message flash
     * 
     * @param string $type Type de message (success, error, warning, info)
     * @param string $message Contenu du message
     */
    protected function setFlashMessage($type, $message) {
        $_SESSION['flash_messages'][$type][] = $message;
    }
    
    /**
     * Méthode pour récupérer les messages flash
     * 
     * @return array Messages flash
     */
    protected function getFlashMessages() {
        $flashMessages = $_SESSION['flash_messages'] ?? [];
        
        // Effacer les messages après les avoir récupérés
        unset($_SESSION['flash_messages']);
        
        return $flashMessages;
    }
    
    /**
     * Méthode pour rediriger vers une autre page
     * 
     * @param string $url URL de destination
     */
    protected function redirect($url) {
        header('Location: ' . $this->baseUrl . '/' . $url);
        exit;
    }
}