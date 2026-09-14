<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

// Only teachers can access
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'enseignant' && $_SESSION['role'] !== 'teacher')) {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

$teacher_id = $_SESSION['id'];

try {
    // Get total number of courses by this teacher
    $coursesStmt = $pdo->prepare("SELECT COUNT(*) as total FROM courses WHERE teacher_id = ?");
    $coursesStmt->execute([$teacher_id]);
    $coursesCount = $coursesStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get total number of students (all active students)
    $studentsStmt = $pdo->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'etudiant' AND active = 1");
    $studentsStmt->execute();
    $studentsCount = $studentsStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get total number of discussion questions on this teacher's courses
    $questionsStmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM course_discussions cd
        JOIN courses c ON cd.course_id = c.id
        WHERE c.teacher_id = ?
    ");
    $questionsStmt->execute([$teacher_id]);
    $questionsCount = $questionsStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get total number of quizzes by this teacher
    $quizzesStmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM quizzes q
        JOIN courses c ON q.course_id = c.id
        WHERE c.teacher_id = ?
    ");
    $quizzesStmt->execute([$teacher_id]);
    $quizzesCount = $quizzesStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get total quiz attempts on this teacher's quizzes
    $attemptsStmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM student_quizzes sq
        JOIN quizzes q ON sq.quiz_id = q.id
        JOIN courses c ON q.course_id = c.id
        WHERE c.teacher_id = ?
    ");
    $attemptsStmt->execute([$teacher_id]);
    $attemptsCount = $attemptsStmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo json_encode([
        'status' => 'success',
        'courses' => $coursesCount,
        'students' => $studentsCount,
        'questions' => $questionsCount,
        'quizzes' => $quizzesCount,
        'attempts' => $attemptsCount
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>