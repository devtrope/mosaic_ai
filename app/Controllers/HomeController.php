<?php
namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HomeController
{
    public function index()
    {
        $projects = [
            ['name' => 'Projet Alpha', 'description' => 'Gestion des tâches pour Alpha'],
            ['name' => 'Projet Beta', 'description' => 'Suivi du développement Beta'],
            ['name' => 'Projet Gamma', 'description' => 'Organisation du projet Gamma'],
        ];

        $loader = new FilesystemLoader(__DIR__ . '/../Views/templates');
        $twig = new Environment($loader);
        echo $twig->render('home.html.twig', ['projects' => $projects]);
    }
} 