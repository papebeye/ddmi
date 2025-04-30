<?php

namespace App\Libraries;

/**
 * Bibliothèque d'authentification pour l'application DDMI
 * (Version compatible PDO)
 */
class Auth {
    /**
     * Connexion à la base de données
     * 
     * @var \PDO
     */
    private $db;
    
    /**
     * Utilisateur actuellement connecté
     * 
     * @var array|null
     */
    private $currentUser = null;
    
    /**
     * Constructeur
     * 
     * @param \PDO $db Connexion à la base de données
     */
    public function __construct($db) {
        $this->db = $db;
        
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialiser l'utilisateur connecté
        $this->initCurrentUser();
    }
    
    /**
     * Initialise l'utilisateur actuel à partir de la session
     */
    private function initCurrentUser() {
        // Vérifier si l'utilisateur est en session
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            
            // Récupérer l'utilisateur depuis la base de données
            $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ? AND actif = 1");
            
            if ($stmt) {
                $stmt->execute([$userId]);
                
                if ($stmt->rowCount() === 1) {
                    $this->currentUser = $stmt->fetch(\PDO::FETCH_ASSOC);
                    return;
                }
            }
            
            // L'utilisateur n'existe pas ou n'est pas actif, supprimer la session
            $this->logout();
        }
        
        // Vérifier s'il y a un cookie de rappel
        if (isset($_COOKIE['ddmi_remember'])) {
            $this->loginFromRememberToken($_COOKIE['ddmi_remember']);
        }
    }
    
    /**
     * Connecte un utilisateur avec ses identifiants
     * Utilise SHA-256 pour la vérification du mot de passe
     * 
     * @param string $username Nom d'utilisateur
     * @param string $password Mot de passe
     * @param bool $remember Créer un cookie de rappel
     * @return bool True si la connexion a réussi
     */
    public function login($username, $password, $remember = false) {
        // Récupérer l'utilisateur
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE username = ? AND actif = 1");
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() !== 1) {
            return false;
        }
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Vérifier le mot de passe (en utilisant SHA-256)
        $hashedPassword = hash('sha256', $password);
        
        if ($hashedPassword !== $user['password']) {
            return false;
        }
        
        // Enregistrer l'utilisateur en session
        $_SESSION['user_id'] = $user['id'];
        $this->currentUser = $user;
        
        // Créer un cookie de rappel si demandé
        if ($remember) {
            $this->createRememberToken($user['id']);
        }
        
        return true;
    }
    
    /**
     * Connecte un utilisateur à partir d'un token de rappel
     * 
     * @param string $token Token de rappel
     * @return bool True si la connexion a réussi
     */
    private function loginFromRememberToken($token) {
        list($selector, $validator) = explode(':', $token);
        
        // Rechercher le sélecteur dans la base de données
        $stmt = $this->db->prepare("SELECT * FROM user_tokens WHERE selector = ? AND expires > NOW()");
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->execute([$selector]);
        
        if ($stmt->rowCount() !== 1) {
            return false;
        }
        
        $tokenRow = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Vérifier le validateur
        if (!hash_equals($tokenRow['token'], hash('sha256', $validator))) {
            return false;
        }
        
        // Récupérer l'utilisateur
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ? AND actif = 1");
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->execute([$tokenRow['user_id']]);
        
        if ($stmt->rowCount() !== 1) {
            return false;
        }
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Enregistrer l'utilisateur en session
        $_SESSION['user_id'] = $user['id'];
        $this->currentUser = $user;
        
        // Renouveler le token
        $this->createRememberToken($user['id']);
        
        return true;
    }
    
    /**
     * Crée un token de rappel pour l'utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     */
    private function createRememberToken($userId) {
        // Vérifier si la table user_tokens existe
        $tableExists = $this->db->query("SHOW TABLES LIKE 'user_tokens'")->rowCount() > 0;
        
        if (!$tableExists) {
            // Créer la table si elle n'existe pas
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS `user_tokens` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `selector` varchar(255) NOT NULL,
                  `token` varchar(255) NOT NULL,
                  `expires` datetime NOT NULL,
                  `type` varchar(20) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `selector` (`selector`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
        
        // Générer un nouveau sélecteur et validateur
        $selector = bin2hex(random_bytes(12));
        $validator = bin2hex(random_bytes(32));
        
        // Définir la date d'expiration (30 jours)
        $expires = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);
        
        // Supprimer les anciens tokens pour cet utilisateur
        $stmt = $this->db->prepare("DELETE FROM user_tokens WHERE user_id = ? AND type = 'remember'");
        
        if ($stmt) {
            $stmt->execute([$userId]);
        }
        
        // Sauvegarder le nouveau token
        $stmt = $this->db->prepare("INSERT INTO user_tokens (user_id, selector, token, expires, type) VALUES (?, ?, ?, ?, 'remember')");
        
        if ($stmt) {
            $hashedValidator = hash('sha256', $validator);
            $stmt->execute([$userId, $selector, $hashedValidator, $expires]);
        }
        
        // Créer le cookie
        $cookieValue = $selector . ':' . $validator;
        setcookie('ddmi_remember', $cookieValue, time() + 60 * 60 * 24 * 30, '/', '', false, true);
    }
    
    /**
     * Déconnecte l'utilisateur
     */
    public function logout() {
        // Supprimer la session
        session_unset();
        session_destroy();
        
        // Supprimer le cookie de rappel
        if (isset($_COOKIE['ddmi_remember'])) {
            setcookie('ddmi_remember', '', time() - 3600, '/', '', false, true);
        }
        
        // Réinitialiser l'utilisateur actuel
        $this->currentUser = null;
    }
    
    /**
     * Vérifie si un utilisateur est connecté
     * 
     * @return bool True si un utilisateur est connecté
     */
    public function isLoggedIn() {
        return $this->currentUser !== null;
    }
    
    /**
     * Vérifie si l'utilisateur connecté est un administrateur
     * 
     * @return bool True si l'utilisateur est un administrateur
     */
    public function isAdmin() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return $this->currentUser['is_admin'] == 1;
    }
    
    /**
     * Vérifie si l'utilisateur a un rôle central
     * 
     * @return bool True si l'utilisateur a un rôle central
     */
    public function isCentralRole() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return $this->currentUser['role'] === 'central';
    }
    
    /**
     * Retourne l'utilisateur actuellement connecté
     * 
     * @return array|null Données de l'utilisateur connecté ou null si aucun
     */
    public function getCurrentUser() {
        return $this->currentUser;
    }
    
    /**
     * Retourne le pays associé à l'utilisateur
     * 
     * @return int|null ID du pays ou null si aucun pays associé
     */
    public function getUserCountry() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->currentUser['pays_id'];
    }
    
    /**
     * Retourne l'année associée à l'utilisateur
     * 
     * @return string|null Année ou null si aucune année associée
     */
    public function getUserYear() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->currentUser['annee'];
    }
}