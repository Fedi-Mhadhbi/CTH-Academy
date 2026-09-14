<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$user_id = $_SESSION['id'];
$data = json_decode(file_get_contents('php://input'), true);

$bio = $data['bio'] ?? null;
$phone = $data['phone'] ?? null;
$location = $data['location'] ?? null;

try {
    $stmt = $pdo->prepare("
        UPDATE users 
        SET bio = ?, phone = ?, location = ?
        WHERE id = ?
    ");
    $stmt->execute([$bio, $phone, $location, $user_id]);

    // Update session if name changed (optional for future)
    echo json_encode([
        'status' => 'success',
        'message' => 'Profile updated successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>