<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'enseignant') {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$title = trim($data['title'] ?? '');

if(!$id || !$title) {
    echo json_encode(['status'=>'error','message'=>'Course ID and new title required']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE courses SET title=? WHERE id=? AND teacher_id=?");
    if($stmt->execute([$title, $id, $_SESSION['id']])) {
        echo json_encode(['status'=>'success','message'=>'Course updated successfully']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Database error']);
    }
} catch(PDOException $e) {
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>
