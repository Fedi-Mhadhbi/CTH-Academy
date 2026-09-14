<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

// Only students
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant') {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) {
    echo json_encode(['status'=>'error','message'=>'Quiz ID missing']);
    exit;
}

$student_id = $_SESSION['id']; // FIXED: Use 'id' not 'user_id'

// Check if student already attempted this quiz
$stmt = $pdo->prepare("SELECT score FROM student_quizzes WHERE student_id = ? AND quiz_id = ?");
$stmt->execute([$student_id, $quiz_id]);
$attempt = $stmt->fetch(PDO::FETCH_ASSOC);

if ($attempt) {
    // Get total questions
    $totalStmt = $pdo->prepare("SELECT COUNT(*) as total FROM questions WHERE quiz_id=?");
    $totalStmt->execute([$quiz_id]);
    $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'attempted' => true,
        'score' => $attempt['score'],
        'total' => $totalResult['total']
    ]);
} else {
    echo json_encode([
        'attempted' => false
    ]);
}
?>