<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$user_id = $_SESSION['id'];
$course_id = $_GET['course_id'] ?? null;

try {
    $query = "
        SELECT id, message, response, message_type, created_at 
        FROM ai_chat_history 
        WHERE user_id = ?
    ";
    
    $params = [$user_id];
    
    if ($course_id) {
        $query .= " AND course_id = ?";
        $params[] = $course_id;
    }
    
    $query .= " ORDER BY created_at DESC LIMIT 50";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'history' => $history
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>