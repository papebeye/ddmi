<?php

namespace App\Controllers;

use App\Config\Database;
use App\Libraries\Auth;

/**
 * Contrôleur pour le module de paramétrage
 */
class ParametrageController extends BaseController {
    /**
     * Connexion à la base de données
     * 
     * @var \PDO
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
     * Affiche la page principale de paramétrage
     */
    public function index() {
        // Vérifier l'authentification
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
            return;
        }
        
        // Récupérer l'utilisateur actuel
        $user = $this->auth->getCurrentUser();
        
        // Récupérer les données pour le paramétrage
        $pays = $this->getPays();
        $annees = $this->getAnnees();
        $utilisateurs = $this->getUtilisateurs();
        
        // Inclure la vue
        include dirname(__DIR__) . '/Views/parametrage/index.php';
    }
    
    /**
     * Récupère la liste des pays
     * 
     * @return array Liste des pays
     */
    private function getPays() {
        $stmt = $this->db->query("SELECT * FROM pays ORDER BY nom");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère la liste des années de travail
     * 
     * @return array Liste des années
     */
    private function getAnnees() {
        $stmt = $this->db->query("SELECT DISTINCT annee FROM annees_travail ORDER BY annee DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère la liste des utilisateurs
     * 
     * @return array Liste des utilisateurs
     */
    private function getUtilisateurs() {
        // Si l'utilisateur est admin, récupérer tous les utilisateurs
        if ($this->auth->isAdmin() || $this->auth->isCentralRole()) {
            $stmt = $this->db->query("SELECT u.*, p.nom as pays_nom FROM utilisateurs u LEFT JOIN pays p ON u.pays_id = p.id ORDER BY u.nom, u.prenom");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        // Sinon, récupérer seulement les utilisateurs du même pays
        $pays_id = $this->auth->getUserCountry();
        if ($pays_id) {
            $stmt = $this->db->prepare("SELECT u.*, p.nom as pays_nom FROM utilisateurs u LEFT JOIN pays p ON u.pays_id = p.id WHERE u.pays_id = ? ORDER BY u.nom, u.prenom");
            $stmt->execute([$pays_id]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        return [];
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