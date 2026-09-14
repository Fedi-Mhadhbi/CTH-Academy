<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['role'])) {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

$discussion_id = $_GET['discussion_id'] ?? null;

if (!$discussion_id) {
    echo json_encode(['status'=>'error','message'=>'Discussion ID required']);
    exit;
}

try {
    // Get discussion details
    $discussionStmt = $pdo->prepare("
        SELECT 
            cd.question,
            cd.created_at,
            u.nom,
            u.prenom
        FROM course_discussions cd
        JOIN users u ON cd.student_id = u.id
        WHERE cd.id = ?
    ");
    $discussionStmt->execute([$discussion_id]);
    $discussion = $discussionStmt->fetch(PDO::FETCH_ASSOC);

    // Get all replies
    $repliesStmt = $pdo->prepare("
        SELECT 
            dr.id,
            dr.reply,
            dr.is_teacher_reply,
            dr.helpful_count,
            dr.created_at,
            u.nom,
            u.prenom,
            u.role
        FROM discussion_replies dr
        JOIN users u ON dr.user_id = u.id
        WHERE dr.discussion_id = ?
        ORDER BY dr.is_teacher_reply DESC, dr.helpful_count DESC, dr.created_at ASC
    ");
    $repliesStmt->execute([$discussion_id]);
    $replies = $repliesStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'discussion' => $discussion,
        'replies' => $replies
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>