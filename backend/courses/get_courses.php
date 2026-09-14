<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

// Only teachers can see courses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'enseignant') {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->query("SELECT * FROM courses ORDER BY id DESC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($courses);
?>