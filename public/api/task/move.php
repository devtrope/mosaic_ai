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
$beforeTaskId = isset($data['before_task_id']) ? (int)$data['before_task_id'] : null;
if (!$taskId || !$colId) {
    echo json_encode(['success' => false, 'error' => 'ID invalide']);
    exit;
}
try {
    $pdo = Database::getInstance()->getConnection();
    $pdo->beginTransaction();
    // On récupère la position cible
    if ($beforeTaskId) {
        $stmt = $pdo->prepare('SELECT position FROM tasks WHERE id = ? AND column_id = ?');
        $stmt->execute([$beforeTaskId, $colId]);
        $pos = $stmt->fetchColumn();
        if ($pos === false) {
            throw new Exception('Tâche de référence introuvable');
        }
        // Décaler les tâches suivantes
        $pdo->prepare('UPDATE tasks SET position = position + 1 WHERE column_id = ? AND position >= ?')->execute([$colId, $pos]);
    } else {
        // À la fin
        $stmt = $pdo->prepare('SELECT IFNULL(MAX(position),0)+1 FROM tasks WHERE column_id = ?');
        $stmt->execute([$colId]);
        $pos = $stmt->fetchColumn();
    }
    // Déplacer la tâche
    $pdo->prepare('UPDATE tasks SET column_id = ?, position = ? WHERE id = ?')->execute([$colId, $pos, $taskId]);
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 