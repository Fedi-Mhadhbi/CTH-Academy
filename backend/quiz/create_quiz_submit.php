<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

// Only teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'enseignant') {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

if(!isset($_SESSION['id'])){
    echo json_encode(['status'=>'error','message'=>'Teacher not logged in']);
    exit;
}

// Read JSON data
$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'] ?? null;
$title = trim($data['title'] ?? '');
$questions = $data['questions'] ?? [];

if (!$course_id || !$title || empty($questions)) {
    echo json_encode(['status'=>'error','message'=>'All fields are required']);
    exit;
}

try {
    // Insert quiz
    $stmtQuiz = $pdo->prepare("INSERT INTO quizzes (course_id, title, created_by) VALUES (?,?,?)");
    $stmtQuiz->execute([$course_id, $title, $_SESSION['id']]);
    $quiz_id = $pdo->lastInsertId();

    // Insert questions
    $stmtQ = $pdo->prepare("
        INSERT INTO questions 
        (quiz_id, question, answer1, answer2, answer3, answer4, correct_answer) 
        VALUES (?,?,?,?,?,?,?)
    ");

    foreach($questions as $q){
        $stmtQ->execute([
            $quiz_id,
            $q['question'],
            $q['option1'],
            $q['option2'],
            $q['option3'],
            $q['option4'],
            $q['correct_option']
        ]);
    }

    echo json_encode(['status'=>'success','message'=>'Quiz and questions added successfully']);

} catch(Exception $e){
    echo json_encode(['status'=>'error','message'=>'Database error: '.$e->getMessage()]);
}