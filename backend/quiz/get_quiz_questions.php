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

// Fetch questions for this quiz
$stmt = $pdo->prepare("
    SELECT 
        id, 
        question, 
        answer1 AS option1, 
        answer2 AS option2, 
        answer3 AS option3, 
        answer4 AS option4, 
        correct_answer AS correct_option 
    FROM questions 
    WHERE quiz_id = ?
");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return as JSON
echo json_encode($questions);