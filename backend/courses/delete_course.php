<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'enseignant') {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

$id = $_GET['id'] ?? null;
if(!$id) {
    echo json_encode(['status'=>'error','message'=>'Course ID required']);
    exit;
}

// Delete course
try {
    // First get PDF path to remove file
    $stmt = $pdo->prepare("SELECT file_path FROM courses WHERE id=? AND teacher_id=?");
    $stmt->execute([$id, $_SESSION['id']]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if($course) {
        // Remove PDF file
        if(file_exists('../../'.$course['file_path'])) unlink('../../'.$course['file_path']);

        // Delete from DB
        $del = $pdo->prepare("DELETE FROM courses WHERE id=? AND teacher_id=?");
        $del->execute([$id, $_SESSION['id']]);

        echo json_encode(['status'=>'success','message'=>'Course deleted successfully']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Course not found or not yours']);
    }
} catch(PDOException $e) {
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>