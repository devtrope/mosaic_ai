<?php
require_once '../../../app/Core/Database.php';

use App\Core\Database;

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name'])) {
    echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
    exit;
}

$name = trim($data['name']);
$description = isset($data['description']) ? trim($data['description']) : '';

$db = Database::getInstance()->getConnection();

$stmt = $db->prepare('INSERT INTO projects (name, description) VALUES (?, ?)');
$stmt->execute([$name, $description]);
$projectId = $db->lastInsertId();

$project = [ 'id' => $projectId, 'name' => $name, 'description' => $description ];
echo json_encode(['success' => true, 'project' => $project]); 