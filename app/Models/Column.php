<?php
namespace App\Models;

use App\Core\Database;

class Column
{
    public $id;
    public $project_id;
    public $title;
    public $position;
    public $tasks = [];

    public static function allByProject($projectId)
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare('SELECT * FROM columns WHERE project_id = ? ORDER BY position, id');
        $stmt->execute([$projectId]);
        $columns = [];
        foreach ($stmt->fetchAll() as $row) {
            $column = new self();
            $column->id = $row['id'];
            $column->project_id = $row['project_id'];
            $column->title = $row['title'];
            $column->position = $row['position'];
            $column->tasks = \App\Models\Task::allByColumn($column->id);
            $columns[] = $column;
        }
        return $columns;
    }
} 