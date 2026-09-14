<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant') {
    echo json_encode(['status'=>'error','message'=>'Only students can post questions']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'] ?? null;
$question = $data['question'] ?? null;

if (!$course_id || !$question) {
    echo json_encode(['status'=>'error','message'=>'Course ID and question required']);
    exit;
}

$student_id = $_SESSION['id'];

try {
    // Insert question
    $stmt = $pdo->prepare("
        INSERT INTO course_discussions (course_id, student_id, question) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$course_id, $student_id, $question]);
    
    $discussion_id = $pdo->lastInsertId();

    // Get course info and teacher
    $courseStmt = $pdo->prepare("SELECT title, teacher_id FROM courses WHERE id = ?");
    $courseStmt->execute([$course_id]);
    $course = $courseStmt->fetch(PDO::FETCH_ASSOC);

    // Create notification for teacher
    if ($course && $course['teacher_id']) {
        $notifStmt = $pdo->prepare("
            INSERT INTO notifications (user_id, message, is_read) 
            VALUES (?, ?, 0)
        ");
        $studentName = $_SESSION['prenom'] . ' ' . $_SESSION['nom'];
        $message = "$studentName asked a question in '{$course['title']}'";
        $notifStmt->execute([$course['teacher_id'], $message]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Question posted successfully',
        'discussion_id' => $discussion_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>