<?php

namespace App\Config;

class Database {
    /**
     * Configuration de la base de données pour le système DDMI
     */
    
    // Configuration par défaut
    public $default = [
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'port'      => 3306,
        'database'  => 'ddmi_db',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
        'debug'     => true,  // false en production
    ];
    
    // Configuration pour les tests
    public $tests = [
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'port'      => 3306,
        'database'  => 'ddmi_db_test',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
        'debug'     => true,
    ];
    
    /**
     * Constructeur qui charge les variables d'environnement
     */
    public function __construct() {
        // Charger les variables depuis .env
        $this->default['host'] = getenv('DB_HOST') ?: $this->default['host'];
        $this->default['port'] = (int) (getenv('DB_PORT') ?: $this->default['port']);
        $this->default['database'] = getenv('DB_DATABASE') ?: $this->default['database'];
        $this->default['username'] = getenv('DB_USERNAME') ?: $this->default['username'];
        $this->default['password'] = getenv('DB_PASSWORD') ?: $this->default['password'];
        $this->default['driver'] = getenv('DB_CONNECTION') ?: $this->default['driver'];
        
        // Désactiver le débogage en production
        if (getenv('APP_ENV') === 'production') {
            $this->default['debug'] = false;
        }
    }
    
    /**
     * Retourne la configuration active en fonction de l'environnement
     */
    public function getActiveConfig() {
        $env = getenv('APP_ENV') ?: 'development';
        
        if ($env === 'testing') {
            return $this->tests;
        }
        
        return $this->default;
    }
    
    /**
     * Établit une connexion à la base de données
     * 
     * @return \PDO|null Connexion à la base de données ou null en cas d'erreur
     */
    public function connect() {
        $config = $this->getActiveConfig();
        
        try {
            // Construire le DSN
            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );
            
            // Options PDO
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            // Créer la connexion PDO
            $pdo = new \PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $options
            );
            
            return $pdo;
        } catch (\PDOException $e) {
            if ($config['debug']) {
                echo $e->getMessage();
            }
            
            // Journaliser l'erreur
            error_log($e->getMessage());
            
            return null;
        }
    }
}