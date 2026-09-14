<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

// Only students
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant') {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

// Get data from request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['status'=>'error','message'=>'Invalid JSON data']);
    exit;
}

$quiz_id = $data['quiz_id'] ?? null;
$score = $data['score'] ?? null;

if (!$quiz_id || $score === null) {
    echo json_encode(['status'=>'error','message'=>'Missing quiz_id or score']);
    exit;
}

$student_id = $_SESSION['id']; // FIXED: Use 'id' not 'user_id'

try {
    // Get total number of questions for this quiz
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM questions WHERE quiz_id = ?");
    $stmt->execute([$quiz_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_questions = $result['total'];

    // Check if student already attempted this quiz
    $checkStmt = $pdo->prepare("SELECT id, score FROM student_quizzes WHERE student_id = ? AND quiz_id = ?");
    $checkStmt->execute([$student_id, $quiz_id]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Already attempted - don't allow retake
        echo json_encode([
            'status' => 'error',
            'message' => 'Quiz already attempted. You can view your score from the quiz list.'
        ]);
        exit;
    }

    // Insert new attempt (first time taking quiz)
    $insertStmt = $pdo->prepare("
        INSERT INTO student_quizzes (student_id, quiz_id, score, taken_at) 
        VALUES (?, ?, ?, NOW())
    ");
    $insertStmt->execute([$student_id, $quiz_id, $score]);

    // Return success
    echo json_encode([
        'status' => 'success',
        'message' => 'Quiz submitted successfully',
        'score' => $score,
        'total' => $total_questions
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>