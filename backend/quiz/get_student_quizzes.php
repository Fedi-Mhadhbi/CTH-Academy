<?php 
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

// Only students
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant') {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

// Fetch all quizzes
$stmt = $pdo->query("
    SELECT q.id AS quiz_id, q.title AS quiz_title, c.title AS course_title
    FROM quizzes q
    JOIN courses c ON q.course_id = c.id
    ORDER BY q.id DESC
");
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Remove duplicates just in case
$uniqueQuizzes = [];
foreach($quizzes as $q){
    if(!isset($uniqueQuizzes[$q['quiz_id']])){
        $uniqueQuizzes[$q['quiz_id']] = $q;
    }
}

echo json_encode(array_values($uniqueQuizzes));