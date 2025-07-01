<?php
namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\Project;

class HomeController
{
    public function index()
    {
        $projects = Project::all();
        $loader = new FilesystemLoader(__DIR__ . '/../Views/templates');
        $twig = new Environment($loader);
        echo $twig->render('home.html.twig', ['projects' => $projects]);
    }
} 