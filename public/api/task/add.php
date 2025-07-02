<?php
require_once '../../../app/Core/Database.php';
require_once '../../../app/Models/Task.php';

use App\Core\Database;

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['column_id'], $data['title'])) {
    echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
    exit;
}

$column_id = (int)$data['column_id'];
$title = trim($data['title']);
$description = isset($data['description']) ? trim($data['description']) : '';
$labels = isset($data['labels']) && is_array($data['labels']) ? $data['labels'] : [];

$db = Database::getInstance()->getConnection();

// Déterminer la position (fin)
$stmt = $db->prepare('SELECT MAX(position) FROM tasks WHERE column_id = ?');
$stmt->execute([$column_id]);
$maxPos = $stmt->fetchColumn();
$position = $maxPos !== false ? $maxPos + 1 : 1;

// Insérer la tâche
$stmt = $db->prepare('INSERT INTO tasks (column_id, title, description, position) VALUES (?, ?, ?, ?)');
$stmt->execute([$column_id, $title, $description, $position]);
$taskId = $db->lastInsertId();

// Associer les labels
if (!empty($labels)) {
    $insert = $db->prepare('INSERT INTO task_labels (task_id, label_id) VALUES (?, ?)');
    foreach ($labels as $labelId) {
        $insert->execute([$taskId, $labelId]);
    }
}

// Récupérer les labels de la tâche (nom + couleur)
$labelsData = [];
if (!empty($labels)) {
    $in = implode(',', array_fill(0, count($labels), '?'));
    $sql = "SELECT id, name, color FROM labels WHERE id IN ($in)";
    $stmt = $db->prepare($sql);
    $stmt->execute($labels);
    $labelsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$task = [ 'id' => $taskId, 'title' => $title, 'description' => $description, 'labels' => $labelsData ];
echo json_encode(['success' => true, 'task' => $task]); 