<?php
namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\Project;

class HomeController
{
    public function index()
    {
        // Création d'un projet en POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            $name = trim($_POST['name']);
            $description = trim($_POST['description'] ?? '');
            if ($name) {
                Project::create($name, $description);
                header('Location: /');
                exit;
            }
        }
        $projects = Project::all();
        $loader = new FilesystemLoader(__DIR__ . '/../Views/templates');
        $twig = new Environment($loader);
        echo $twig->render('home.html.twig', ['projects' => $projects]);
    }
} 