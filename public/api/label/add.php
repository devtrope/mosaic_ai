<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Core\Database;

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['name'], $data['color'], $data['project_id'])) {
    echo json_encode(['success' => false, 'error' => 'Paramètres manquants', 'debug' => $data]);
    exit;
}
$name = trim($data['name']);
$color = trim($data['color']);
$projectId = $data['project_id'] !== '' ? (int)$data['project_id'] : null;
if (!$name || !$color) {
    echo json_encode(['success' => false, 'error' => 'Nom ou couleur manquant', 'debug' => $data]);
    exit;
}
try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare('INSERT INTO labels (name, color, project_id) VALUES (?, ?, ?)');
    $stmt->execute([$name, $color, $projectId]);
    $id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'label' => ['id' => $id, 'name' => $name, 'color' => $color], 'debug' => $data]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'debug' => $data]);
} 