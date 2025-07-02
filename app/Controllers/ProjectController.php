<?php
namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\Project;
use App\Models\Column;
use App\Models\Task;
use App\Models\Label;

class ProjectController
{
    public function show($projectId)
    {
        // Création d'une colonne en POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['column_title'])) {
            $title = trim($_POST['column_title']);
            if ($title) {
                Column::create($projectId, $title);
                header('Location: /project/' . $projectId);
                exit;
            }
        }
        // Création d'une tâche en POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_column_id'], $_POST['task_title'])) {
            $colId = (int)$_POST['task_column_id'];
            $title = trim($_POST['task_title']);
            $desc = trim($_POST['task_description'] ?? '');
            if ($colId && $title) {
                Task::create($colId, $title, $desc);
                header('Location: /project/' . $projectId);
                exit;
            }
        }
        // Modification d'une tâche en POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task_id'])) {
            $taskId = (int)$_POST['edit_task_id'];
            $title = trim($_POST['edit_task_title']);
            $desc = trim($_POST['edit_task_description'] ?? '');
            $labels = isset($_POST['edit_task_labels']) ? $_POST['edit_task_labels'] : [];
            Task::update($taskId, $title, $desc, $labels);
            header('Location: /project/' . $projectId);
            exit;
        }
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
        // Charger tous les libellés pour l'édition
        $labels = Label::all();
        $loader = new FilesystemLoader(__DIR__ . '/../Views/templates');
        $twig = new Environment($loader);
        echo $twig->render('project.html.twig', [
            'project' => $project,
            'kanban' => $kanban,
            'labels' => $labels
        ]);
    }
} 