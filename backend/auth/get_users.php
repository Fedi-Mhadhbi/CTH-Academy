<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status"=>"error","message"=>"Access denied"]);
    exit;
}

// Fetch all users (no quiz_status)
$stmt = $pdo->query("SELECT id, nom, prenom, email, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count teachers and students
$teachers = 0;
$students = 0;
foreach ($users as $u) {
    if ($u['role'] === 'enseignant') $teachers++;
    if ($u['role'] === 'etudiant') $students++;
}

// Return JSON
echo json_encode([
    "status" => "success",
    "users" => $users,
    "teachers" => $teachers,
    "students" => $students
]);
