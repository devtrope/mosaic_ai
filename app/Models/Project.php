<?php
namespace App\Models;

use App\Core\Database;

class Project
{
    public $id;
    public $name;
    public $description;

    public function __construct($name = null, $description = null)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public static function all()
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->query('SELECT * FROM projects ORDER BY id DESC');
        $results = $stmt->fetchAll();
        $projects = [];
        foreach ($results as $row) {
            $project = new self();
            $project->id = $row['id'];
            $project->name = $row['name'];
            $project->description = $row['description'];
            $projects[] = $project;
        }
        return $projects;
    }

    public static function find($id)
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $project = new self();
            $project->id = $row['id'];
            $project->name = $row['name'];
            $project->description = $row['description'];
            return $project;
        }
        return null;
    }

    public static function create($name, $description)
    {
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare('INSERT INTO projects (name, description) VALUES (?, ?)');
        $stmt->execute([$name, $description]);
        return $pdo->lastInsertId();
    }
} 