<?php
namespace App\Models;

use App\Core\Database;

class Label
{
    public $id;
    public $name;
    public $color;

    public static function allByTask($taskId)
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare('SELECT l.id, l.name, l.color FROM labels l INNER JOIN task_labels tl ON l.id = tl.label_id WHERE tl.task_id = ?');
        $stmt->execute([$taskId]);
        $labels = [];
        foreach ($stmt->fetchAll() as $row) {
            $label = new self();
            $label->id = $row['id'];
            $label->name = $row['name'];
            $label->color = $row['color'];
            $labels[] = $label;
        }
        return $labels;
    }

    public static function all()
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->query('SELECT * FROM labels ORDER BY name');
        $labels = [];
        foreach ($stmt->fetchAll() as $row) {
            $label = new self();
            $label->id = $row['id'];
            $label->name = $row['name'];
            $label->color = $row['color'];
            $labels[] = $label;
        }
        return $labels;
    }

    public static function allByProject($projectId)
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare('SELECT * FROM labels WHERE project_id IS NULL OR project_id = ? ORDER BY name');
        $stmt->execute([$projectId]);
        $labels = [];
        foreach ($stmt->fetchAll() as $row) {
            $label = new self();
            $label->id = $row['id'];
            $label->name = $row['name'];
            $label->color = $row['color'];
            $labels[] = $label;
        }
        return $labels;
    }
} 