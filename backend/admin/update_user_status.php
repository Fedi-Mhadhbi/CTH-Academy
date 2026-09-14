<?php
session_start();
header('Content-Type: application/json');
require_once "../config/db.php";

// Only admin can manage users
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
  echo json_encode(["status"=>"error","message"=>"Unauthorized access"]);
  exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$user_id = $data['user_id'] ?? null;
$action = $data['action'] ?? null;

if (!$user_id || !$action) {
    echo json_encode(["status"=>"error","message"=>"Missing user_id or action"]);
    exit;
}

try {
    if ($action === 'approve') {
        // Approve user (set active = 1)
        $stmt = $pdo->prepare("UPDATE users SET active = 1 WHERE id = ?");
        $stmt->execute([$user_id]);
        
        echo json_encode([
            "status" => "success",
            "message" => "User approved successfully"
        ]);
        
    } elseif ($action === 'delete') {
        // Check if user exists and get their role
        $checkStmt = $pdo->prepare("SELECT id, role FROM users WHERE id = ?");
        $checkStmt->execute([$user_id]);
        $user = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo json_encode([
                "status" => "error",
                "message" => "User not found"
            ]);
            exit;
        }
        
        // Start transaction for safe deletion
        $pdo->beginTransaction();
        
        try {
            // Delete based on role
            if ($user['role'] === 'etudiant' || $user['role'] === 'student') {
                // Delete student-specific data
                $pdo->prepare("DELETE FROM student_quizzes WHERE student_id = ?")->execute([$user_id]);
                $pdo->prepare("DELETE FROM quiz_attempts WHERE user_id = ?")->execute([$user_id]);
                $pdo->prepare("DELETE FROM discussion_replies WHERE user_id = ?")->execute([$user_id]);
                $pdo->prepare("DELETE FROM course_discussions WHERE student_id = ?")->execute([$user_id]);
                
            } elseif ($user['role'] === 'enseignant' || $user['role'] === 'teacher') {
                // For teachers, we need to handle courses differently
                // Option 1: Delete all their courses (and cascade to quizzes/questions)
                $pdo->prepare("DELETE FROM courses WHERE teacher_id = ?")->execute([$user_id]);
                
                // Delete discussion replies
                $pdo->prepare("DELETE FROM discussion_replies WHERE user_id = ?")->execute([$user_id]);
            }
            
            // Delete common data for all users
            $pdo->prepare("DELETE FROM notifications WHERE user_id = ?")->execute([$user_id]);
            $pdo->prepare("DELETE FROM activity_logs WHERE user_id = ?")->execute([$user_id]);
            
            // Finally, delete the user
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
            
            // Commit transaction
            $pdo->commit();
            
            echo json_encode([
                "status" => "success",
                "message" => "User and all associated data deleted successfully"
            ]);
            
        } catch (Exception $e) {
            // Rollback on error
            $pdo->rollBack();
            throw $e;
        }
        
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid action. Use 'approve' or 'delete'"
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>