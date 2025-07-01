<?php
namespace App\Core;

class Router
{
    public function dispatch()
    {
        // Simple routage pour la page d'accueil et la page projet
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if ($uri === '/' || $uri === '/index.php') {
            $controller = new \App\Controllers\HomeController();
            $controller->index();
        } elseif (preg_match('#^/project/(\d+)$#', $uri, $matches)) {
            $controller = new \App\Controllers\ProjectController();
            $controller->show($matches[1]);
        } else {
            http_response_code(404);
            echo 'Page non trouvée';
        }
    }
} 