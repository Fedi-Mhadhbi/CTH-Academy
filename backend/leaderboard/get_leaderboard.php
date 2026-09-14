<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

// Only logged-in users can view leaderboard
if (!isset($_SESSION['role'])) {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

try {
    // Get top students by total quiz scores
    $stmt = $pdo->query("
        SELECT 
            u.id,
            u.nom,
            u.prenom,
            COUNT(DISTINCT sq.quiz_id) as quizzes_taken,
            SUM(sq.score) as total_score,
            ROUND(AVG(sq.score * 100.0 / (
                SELECT COUNT(*) FROM questions WHERE quiz_id = sq.quiz_id
            )), 1) as avg_percentage
        FROM users u
        INNER JOIN student_quizzes sq ON u.id = sq.student_id
        WHERE u.role = 'etudiant'
        GROUP BY u.id, u.nom, u.prenom
        HAVING quizzes_taken > 0
        ORDER BY total_score DESC, avg_percentage DESC
        LIMIT 10
    ");
    
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add rank to each student
    $rank = 1;
    foreach ($leaderboard as &$student) {
        $student['rank'] = $rank++;
    }
    
    echo json_encode([
        'status' => 'success',
        'leaderboard' => $leaderboard
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>