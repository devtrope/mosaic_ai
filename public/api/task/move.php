<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Core\Database;

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['task_id'], $data['column_id'])) {
    echo json_encode(['success' => false, 'error' => 'Paramètres manquants']);
    exit;
}
$taskId = (int)$data['task_id'];
$colId = (int)$data['column_id'];
if (!$taskId || !$colId) {
    echo json_encode(['success' => false, 'error' => 'ID invalide']);
    exit;
}
try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare('UPDATE tasks SET column_id = ? WHERE id = ?');
    $stmt->execute([$colId, $taskId]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 