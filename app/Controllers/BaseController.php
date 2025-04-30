<?php

namespace App\Controllers;

class BaseController {
    protected $viewData = [];
    
    protected function render($view, array $data = []) {
        // Logique de rendu de vue simplifiée
        echo "Rendu de la vue: {$view}";
    }
}
