<?php
session_start();
require_once "../config/db.php";

if(!isset($_SESSION['role']) || $_SESSION['role']!=='admin'){
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}

// Stats
$teachers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='enseignant'")->fetchColumn();
$students = $pdo->query("SELECT COUNT(*) FROM users WHERE role='etudiant'")->fetchColumn();
$courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalAssigned = $students * $pdo->query("SELECT COUNT(*) FROM quizzes")->fetchColumn();
$totalTaken = $pdo->query("SELECT COUNT(*) FROM student_quizzes")->fetchColumn();
$completionRate = $totalAssigned>0?round(($totalTaken/$totalAssigned)*100,2):0;

// Users
$stmt = $pdo->query("SELECT id, nom, prenom, email, role, active, (SELECT COUNT(*) FROM student_quizzes WHERE student_id=users.id) as attempted,
    (SELECT IFNULL(SUM(score),0) FROM student_quizzes WHERE student_id=users.id) as total_score
    FROM users ORDER BY role DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent activities
$stmt = $pdo->query("SELECT a.*, u.nom, u.prenom FROM activity_logs a JOIN users u ON a.user_id=u.id ORDER BY created_at DESC LIMIT 10");
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status"=>"success",
    "teachers"=>$teachers,
    "students"=>$students,
    "courses"=>$courses,
    "completed"=>$totalTaken,
    "pending"=>$totalAssigned-$totalTaken,
    "completionRate"=>$completionRate,
    "users"=>$users,
    "activities"=>$activities
]);
?>