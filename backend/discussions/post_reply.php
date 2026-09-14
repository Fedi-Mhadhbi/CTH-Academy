<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['role'])) {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$discussion_id = $data['discussion_id'] ?? null;
$reply = $data['reply'] ?? null;

if (!$discussion_id || !$reply) {
    echo json_encode(['status'=>'error','message'=>'Discussion ID and reply required']);
    exit;
}

$user_id = $_SESSION['id'];
$is_teacher = ($_SESSION['role'] === 'teacher' || $_SESSION['role'] === 'enseignant') ? 1 : 0;

try {
    // Insert reply
    $stmt = $pdo->prepare("
        INSERT INTO discussion_replies (discussion_id, user_id, reply, is_teacher_reply) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$discussion_id, $user_id, $reply, $is_teacher]);
    
    $reply_id = $pdo->lastInsertId();

    // If teacher replied, mark discussion as answered
    if ($is_teacher) {
        $updateStmt = $pdo->prepare("UPDATE course_discussions SET is_answered = 1 WHERE id = ?");
        $updateStmt->execute([$discussion_id]);
    }

    // Get the student who asked the question
    $discussionStmt = $pdo->prepare("SELECT student_id, course_id FROM course_discussions WHERE id = ?");
    $discussionStmt->execute([$discussion_id]);
    $discussion = $discussionStmt->fetch(PDO::FETCH_ASSOC);

    // Notify student if teacher replied
    if ($is_teacher && $discussion) {
        $notifStmt = $pdo->prepare("
            INSERT INTO notifications (user_id, message, is_read) 
            VALUES (?, ?, 0)
        ");
        $teacherName = $_SESSION['prenom'] . ' ' . $_SESSION['nom'];
        $message = "$teacherName replied to your question";
        $notifStmt->execute([$discussion['student_id'], $message]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Reply posted successfully',
        'reply_id' => $reply_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>