<?php

namespace App\Controllers;

class HomeController extends BaseController {
    public function index() {
        // Chemin vers la vue
        $viewPath = dirname(__DIR__) . '/Views/home/index.php';
        
        // Vérifier si la vue existe
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h1>Erreur</h1>";
            echo "<p>La vue n'a pas été trouvée.</p>";
        }
    }
}