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
} 