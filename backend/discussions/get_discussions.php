<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['role'])) {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    echo json_encode(['status'=>'error','message'=>'Course ID required']);
    exit;
}

try {
    // Get all discussions for this course
    $stmt = $pdo->prepare("
        SELECT 
            cd.id,
            cd.question,
            cd.is_answered,
            cd.created_at,
            u.nom,
            u.prenom,
            u.role,
            (SELECT COUNT(*) FROM discussion_replies WHERE discussion_id = cd.id) as reply_count
        FROM course_discussions cd
        JOIN users u ON cd.student_id = u.id
        WHERE cd.course_id = ?
        ORDER BY cd.created_at DESC
    ");
    $stmt->execute([$course_id]);
    $discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'discussions' => $discussions
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>