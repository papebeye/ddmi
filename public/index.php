<?php

/**
 * Point d'entrée de l'application DDMI
 * 
 * Ce fichier initialise l'application et gère les requêtes entrantes
 */

// Chemin vers la racine de l'application
$rootPath = dirname(__DIR__);

// Configuration de l'application
require_once $rootPath . '/vendor/autoload.php';

// URL de base
$basePath = '/ddmi/src/public';
$requestUri = $_SERVER['REQUEST_URI'];

// Supprimer la base de l'URI pour le routage
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Si l'URI est vide, mettre '/' par défaut
if (empty($requestUri) || $requestUri === '/index.php') {
    $requestUri = '/';
}

// Analyser l'URI pour extraire le chemin et les paramètres
$path = parse_url($requestUri, PHP_URL_PATH);

// Connexion à la base de données
$dbConfig = new App\Config\Database();
$db = $dbConfig->connect();

// Initialisation de l'authentification
$auth = new App\Libraries\Auth($db);

// Routage des requêtes
switch ($path) {
    // Page d'accueil
    case '/':
        $controller = new App\Controllers\HomeController();
        $controller->index();
        break;

    // Authentification
    case '/login':
        $controller = new App\Controllers\Auth\LoginController();
        $controller->index();
        break;
        
    // Accepter les deux chemins pour l'authentification
    case '/login/authenticate':
    case '/authenticate':
        $controller = new App\Controllers\Auth\LoginController();
        $controller->authenticate();
        break;
        
    case '/logout':
        $controller = new App\Controllers\Auth\LoginController();
        $controller->logout();
        break;
    
    // Page tableau de bord (protégée)
    case '/dashboard':
        // Vérifier l'authentification
        if (!$auth->isLoggedIn()) {
            // Rediriger vers la page de connexion si non authentifié
            header('Location: ' . $basePath . '/login');
            exit;
        }
        
        $user = $auth->getCurrentUser();
        
        // Afficher le tableau de bord
        echo "<!DOCTYPE html>
              <html lang='fr'>
              <head>
                  <meta charset='UTF-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                  <title>Tableau de bord DDMI</title>
                  <link rel='stylesheet' href='{$basePath}/assets/css/main.css'>
                  <style>
                      body {
                          font-family: Arial, sans-serif;
                          line-height: 1.6;
                          margin: 0;
                          padding: 20px;
                          background-color: #f5f7fa;
                      }
                      .dashboard-header {
                          background-color: #2c6da8;
                          color: white;
                          padding: 20px;
                          border-radius: 5px;
                          margin-bottom: 20px;
                      }
                      .user-info {
                          background-color: white;
                          border-radius: 5px;
                          padding: 20px;
                          margin-bottom: 20px;
                          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                      }
                      .logout-btn {
                          display: inline-block;
                          background-color: #dc3545;
                          color: white;
                          text-decoration: none;
                          padding: 10px 15px;
                          border-radius: 5px;
                          margin-top: 10px;
                      }
                      .module-grid {
                          display: grid;
                          grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                          gap: 20px;
                      }
                      .module-card {
                          background-color: white;
                          border-radius: 5px;
                          padding: 20px;
                          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                      }
                      .module-card h3 {
                          margin-top: 0;
                          color: #2c6da8;
                      }
                  </style>
              </head>
              <body>
                  <div class='dashboard-header'>
                      <h1>Tableau de bord DDMI</h1>
                  </div>
                  
                  <div class='user-info'>
                      <h2>Bienvenue, " . htmlspecialchars($user['prenom'] . ' ' . $user['nom']) . "!</h2>
                      <p><strong>Rôle:</strong> " . htmlspecialchars($user['role']) . "</p>";
        
        if ($user['pays_id']) {
            echo "<p><strong>Pays:</strong> " . htmlspecialchars($user['pays_id']) . "</p>";
        }
        
        if ($user['annee']) {
            echo "<p><strong>Année:</strong> " . htmlspecialchars($user['annee']) . "</p>";
        }
        
        echo "      <a href='{$basePath}/logout' class='logout-btn'>Déconnexion</a>
                  </div>
                  
                  <div class='module-grid'>
                      <div class='module-card'>
                          <h3>Paramétrage</h3>
                          <p>Gestion des paramètres du système DDMI</p>
                          <p><a href='{$basePath}/parametrage'>Accéder au module</a></p>
                      </div>
                      
                      <div class='module-card'>
                          <h3>Import de données</h3>
                          <p>Importation des indicateurs élémentaires</p>
                          <p><a href='{$basePath}/import'>Accéder au module</a></p>
                      </div>
                      
                      <div class='module-card'>
                          <h3>Calcul DDMI</h3>
                          <p>Normalisation et agrégation des indicateurs</p>
                          <p><a href='{$basePath}/calcul'>Accéder au module</a></p>
                      </div>
                      
                      <div class='module-card'>
                          <h3>Visualisation</h3>
                          <p>Tableaux de bord et graphiques</p>
                          <p><a href='{$basePath}/visualisation'>Accéder au module</a></p>
                      </div>
                  </div>
              </body>
              </html>";
        break;
        
    // Module de paramétrage (protégé)
    case '/parametrage':
        // Vérifier l'authentification
        if (!$auth->isLoggedIn()) {
            // Rediriger vers la page de connexion si non authentifié
            header('Location: ' . $basePath . '/login');
            exit;
        }
        
        // Afficher la page de paramétrage
        $controller = new App\Controllers\ParametrageController();
        $controller->index();
        break;
        
    // Page non trouvée
    default:
        // Afficher une page 404
        http_response_code(404);
        echo "<!DOCTYPE html>
              <html lang='fr'>
              <head>
                  <meta charset='UTF-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                  <title>Page non trouvée - DDMI</title>
                  <style>
                      body {
                          font-family: Arial, sans-serif;
                          line-height: 1.6;
                          margin: 0;
                          padding: 20px;
                          background-color: #f5f7fa;
                          text-align: center;
                      }
                      .error-container {
                          max-width: 600px;
                          margin: 50px auto;
                          background-color: white;
                          border-radius: 5px;
                          padding: 20px;
                          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                      }
                      h1 {
                          color: #dc3545;
                      }
                      a {
                          display: inline-block;
                          background-color: #2c6da8;
                          color: white;
                          text-decoration: none;
                          padding: 10px 15px;
                          border-radius: 5px;
                          margin-top: 20px;
                      }
                  </style>
              </head>
              <body>
                  <div class='error-container'>
                      <h1>Page non trouvée</h1>
                      <p>La page demandée n'existe pas.</p>
                      <a href='{$basePath}/'>Retour à l'accueil</a>
                  </div>
              </body>
              </html>";
        break;
}