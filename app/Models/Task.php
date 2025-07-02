<?php
namespace App\Models;

use App\Core\Database;

class Task
{
    public $id;
    public $column_id;
    public $title;
    public $description;
    public $position;
    public $labels;

    public static function allByColumn($columnId)
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare('SELECT * FROM tasks WHERE column_id = ? ORDER BY position ASC, id ASC');
        $stmt->execute([$columnId]);
        $tasks = [];
        foreach ($stmt->fetchAll() as $row) {
            $task = new self();
            $task->id = $row['id'];
            $task->column_id = $row['column_id'];
            $task->title = $row['title'];
            $task->description = $row['description'];
            $task->position = $row['position'];
            $task->labels = \App\Models\Label::allByTask($task->id);
            $tasks[] = $task;
        }
        return $tasks;
    }

    public static function create($columnId, $title, $description)
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare('SELECT IFNULL(MAX(position),0)+1 AS pos FROM tasks WHERE column_id = ?');
        $stmt->execute([$columnId]);
        $pos = $stmt->fetchColumn();
        $stmt = $pdo->prepare('INSERT INTO tasks (column_id, title, description, position) VALUES (?, ?, ?, ?)');
        $stmt->execute([$columnId, $title, $description, $pos]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $title, $desc, $labels)
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE tasks SET title = ?, description = ? WHERE id = ?');
        $stmt->execute([$title, $desc, $id]);
        // Mettre à jour les libellés
        $pdo->prepare('DELETE FROM task_labels WHERE task_id = ?')->execute([$id]);
        if (!empty($labels)) {
            $stmt = $pdo->prepare('INSERT INTO task_labels (task_id, label_id) VALUES (?, ?)');
            foreach ($labels as $labelId) {
                $stmt->execute([$id, $labelId]);
            }
        }
        $pdo->commit();
    }
} 