<?php
session_start();
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$user_id = $_SESSION['id'];

try {
    // Get user profile
    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, email, role, bio, profile_picture, phone, location, created_at 
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        echo json_encode(['status'=>'error','message'=>'User not found']);
        exit;
    }

    // Get statistics based on role
    $stats = [];
    
    if ($profile['role'] === 'etudiant') {
        // Student stats
        // Total quizzes taken
        $quizStmt = $pdo->prepare("SELECT COUNT(*) as total FROM student_quizzes WHERE student_id = ?");
        $quizStmt->execute([$user_id]);
        $stats['quizzes_taken'] = $quizStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Average score
        $avgStmt = $pdo->prepare("
            SELECT ROUND(AVG(sq.score * 100.0 / (SELECT COUNT(*) FROM questions WHERE quiz_id = sq.quiz_id)), 1) as avg_score
            FROM student_quizzes sq
            WHERE sq.student_id = ?
        ");
        $avgStmt->execute([$user_id]);
        $stats['average_score'] = $avgStmt->fetch(PDO::FETCH_ASSOC)['avg_score'] ?: 0;

        // Certificates earned (score >= 75%)
        $certStmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM student_quizzes sq
            WHERE sq.student_id = ?
            AND (sq.score * 100.0 / (SELECT COUNT(*) FROM questions WHERE quiz_id = sq.quiz_id)) >= 75
        ");
        $certStmt->execute([$user_id]);
        $stats['certificates'] = $certStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Discussion questions asked
        $discussionStmt = $pdo->prepare("SELECT COUNT(*) as total FROM course_discussions WHERE student_id = ?");
        $discussionStmt->execute([$user_id]);
        $stats['questions_asked'] = $discussionStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get all certificates
        $certsStmt = $pdo->prepare("
            SELECT sq.*, q.title as quiz_title, c.title as course_title,
                   (sq.score * 100.0 / (SELECT COUNT(*) FROM questions WHERE quiz_id = sq.quiz_id)) as percentage
            FROM student_quizzes sq
            JOIN quizzes q ON sq.quiz_id = q.id
            JOIN courses c ON q.course_id = c.id
            WHERE sq.student_id = ?
            AND (sq.score * 100.0 / (SELECT COUNT(*) FROM questions WHERE quiz_id = sq.quiz_id)) >= 75
            ORDER BY sq.taken_at DESC
        ");
        $certsStmt->execute([$user_id]);
        $stats['certificate_list'] = $certsStmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($profile['role'] === 'enseignant') {
        // Teacher stats
        $coursesStmt = $pdo->prepare("SELECT COUNT(*) as total FROM courses WHERE teacher_id = ?");
        $coursesStmt->execute([$user_id]);
        $stats['courses_created'] = $coursesStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $quizzesStmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            WHERE c.teacher_id = ?
        ");
        $quizzesStmt->execute([$user_id]);
        $stats['quizzes_created'] = $quizzesStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $questionsStmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM course_discussions cd
            JOIN courses c ON cd.course_id = c.id
            WHERE c.teacher_id = ?
        ");
        $questionsStmt->execute([$user_id]);
        $stats['questions_answered'] = $questionsStmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Get achievements
    $achStmt = $pdo->prepare("
        SELECT * FROM user_achievements 
        WHERE user_id = ? 
        ORDER BY earned_at DESC
    ");
    $achStmt->execute([$user_id]);
    $achievements = $achStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'profile' => $profile,
        'stats' => $stats,
        'achievements' => $achievements
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>