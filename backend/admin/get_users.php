<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}

$teachers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='enseignant'")->fetchColumn();
$students = $pdo->query("SELECT COUNT(*) FROM users WHERE role='etudiant'")->fetchColumn();

$stmt = $pdo->query("SELECT id, nom, prenom, email, role, active FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($users as &$u){
    if($u['role'] === 'etudiant'){
        $stmtQ = $pdo->prepare("SELECT COUNT(*) AS attempted, IFNULL(SUM(score),0) AS total_score FROM student_quizzes WHERE student_id=?");
        $stmtQ->execute([$u['id']]);
        $q = $stmtQ->fetch(PDO::FETCH_ASSOC);
        $u['attempted'] = $q['attempted'];
        $u['total_score'] = $q['total_score'];
    } else {
        $u['attempted'] = "-";
        $u['total_score'] = "-";
    }
}

echo json_encode([
    "status"=>"success",
    "teachers"=>$teachers,
    "students"=>$students,
    "users"=>$users
]);