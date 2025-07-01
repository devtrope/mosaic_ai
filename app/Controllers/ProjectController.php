<?php
namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ProjectController
{
    public function show($projectId)
    {
        $project = [
            'id' => $projectId,
            'name' => 'Projet Alpha',
        ];
        $kanban = [
            [
                'title' => 'À faire',
                'tasks' => [
                    ['title' => 'Préparer la réunion', 'description' => 'Organiser la réunion de lancement'],
                    ['title' => 'Créer le repo Git', 'description' => 'Initialiser le dépôt du projet'],
                ]
            ],
            [
                'title' => 'En cours',
                'tasks' => [
                    ['title' => "Développer la page d'accueil", 'description' => 'Coder la vue principale'],
                ]
            ],
            [
                'title' => 'Terminé',
                'tasks' => [
                    ['title' => 'Définir le cahier des charges', 'description' => 'Document validé par le client'],
                ]
            ]
        ];
        $loader = new FilesystemLoader(__DIR__ . '/../Views/templates');
        $twig = new Environment($loader);
        echo $twig->render('project.html.twig', [
            'project' => $project,
            'kanban' => $kanban
        ]);
    }
} 