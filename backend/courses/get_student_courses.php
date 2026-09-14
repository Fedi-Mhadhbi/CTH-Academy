<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant') {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->query("
    SELECT c.id, c.title, c.category, c.file_path, u.nom AS teacher_nom, u.prenom AS teacher_prenom
    FROM courses c
    JOIN users u ON c.teacher_id = u.id
    ORDER BY c.created_at DESC
");

$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($courses);