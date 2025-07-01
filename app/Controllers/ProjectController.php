<?php
namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\Project;
use App\Models\Column;

class ProjectController
{
    public function show($projectId)
    {
        $project = [
            'id' => $projectId,
            'name' => 'Projet inconnu',
        ];
        // Charger le projet depuis la BDD si possible
        $projectObj = Project::find($projectId);
        if ($projectObj) {
            $project['name'] = $projectObj->name;
        }
        $kanban = Column::allByProject($projectId);
        $loader = new FilesystemLoader(__DIR__ . '/../Views/templates');
        $twig = new Environment($loader);
        echo $twig->render('project.html.twig', [
            'project' => $project,
            'kanban' => $kanban
        ]);
    }
} 