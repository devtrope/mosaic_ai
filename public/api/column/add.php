<?php
require_once '../../../app/Core/Database.php';
require_once '../../../app/Models/Column.php';

use App\Core\Database;

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['project_id'], $data['title'])) {
    echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
    exit;
}

$project_id = (int)$data['project_id'];
$title = trim($data['title']);

$db = Database::getInstance()->getConnection();

// Déterminer la position (fin)
$stmt = $db->prepare('SELECT MAX(position) FROM columns WHERE project_id = ?');
$stmt->execute([$project_id]);
$maxPos = $stmt->fetchColumn();
$position = $maxPos !== false ? $maxPos + 1 : 1;

// Insérer la colonne
$stmt = $db->prepare('INSERT INTO columns (project_id, title, position) VALUES (?, ?, ?)');
$stmt->execute([$project_id, $title, $position]);
$colId = $db->lastInsertId();

$column = [ 'id' => $colId, 'title' => $title ];
echo json_encode(['success' => true, 'column' => $column]); 