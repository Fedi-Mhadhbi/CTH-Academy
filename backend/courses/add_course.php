<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

// Only teachers can add courses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'enseignant') {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

// Make sure teacher ID is in session
if (!isset($_SESSION['id'])) {
    echo json_encode(['status'=>'error','message'=>'Teacher ID missing in session']);
    exit;
}

$teacherId = $_SESSION['id'];

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Invalid request method']);
    exit;
}

// Get POST data
$title = trim($_POST['title'] ?? '');
$category = trim($_POST['category'] ?? '');
$pdfFile = $_FILES['pdf'] ?? null;

// Validation
if (!$title || !$pdfFile || !$category) {
    echo json_encode(['status'=>'error','message'=>'Title, category, and PDF are required']);
    exit;
}

// Upload PDF
$uploadDir = '../../uploads/';
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo json_encode(['status'=>'error','message'=>'Failed to create uploads directory']);
        exit;
    }
}

$pdfName = time() . '_' . basename($pdfFile['name']);
$targetPath = $uploadDir . $pdfName;

if (!move_uploaded_file($pdfFile['tmp_name'], $targetPath)) {
    echo json_encode(['status'=>'error','message'=>'Failed to upload PDF']);
    exit;
}

// Insert into DB
try {
    $stmt = $pdo->prepare("
        INSERT INTO courses (title, category, file_path, teacher_id, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");

    if ($stmt->execute([$title, $category, 'uploads/'.$pdfName, $teacherId])) {
        echo json_encode(['status'=>'success','message'=>'Course added successfully']);
    } else {
        $error = $stmt->errorInfo();
        echo json_encode(['status'=>'error','message'=>'Database error: '.$error[2]]);
    }
} catch (PDOException $e) {
    echo json_encode(['status'=>'error','message'=>'Database exception: '.$e->getMessage()]);
}