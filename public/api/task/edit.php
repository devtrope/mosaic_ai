<?php
use App\Core\Database;
require_once '../../../app/Core/Database.php';
require_once '../../../app/Models/Task.php';
require_once '../../../app/Models/Label.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['title'], $data['description'], $data['labels'])) {
    echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
    exit;
}

$id = (int)$data['id'];
$title = trim($data['title']);
$description = trim($data['description']);
$labels = $data['labels'];

$db = Database::getInstance()->getConnection();

// Mettre à jour la tâche
$stmt = $db->prepare('UPDATE tasks SET title = ?, description = ? WHERE id = ?');
$stmt->execute([$title, $description, $id]);

// Mettre à jour les labels (table de liaison)
$db->prepare('DELETE FROM task_labels WHERE task_id = ?')->execute([$id]);
if (!empty($labels)) {
    $insert = $db->prepare('INSERT INTO task_labels (task_id, label_id) VALUES (?, ?)');
    foreach ($labels as $labelId) {
        $insert->execute([$id, $labelId]);
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

echo json_encode(['success' => true, 'labels' => $labelsData]); 