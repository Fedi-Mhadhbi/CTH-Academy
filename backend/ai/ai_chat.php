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

$message = $data['message'] ?? null;
$course_id = $data['course_id'] ?? null;
$message_type = $data['type'] ?? 'question';

if (!$message) {
    echo json_encode(['status'=>'error','message'=>'No message provided']);
    exit;
}

$api_key = getenv('GEMINI_API_KEY');


try {
    // ===== GET COMPLETE USER PROFILE =====
    $userStmt = $pdo->prepare("
        SELECT nom, prenom, email, role, bio, phone, location, created_at 
        FROM users 
        WHERE id = ?
    ");
    $userStmt->execute([$user_id]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    // ===== GET WEBSITE STATISTICS =====
    $statsStmt = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM courses) as total_courses,
            (SELECT COUNT(*) FROM quizzes) as total_quizzes,
            (SELECT COUNT(*) FROM users WHERE role='etudiant') as total_students,
            (SELECT COUNT(*) FROM users WHERE role='enseignant') as total_teachers,
            (SELECT COUNT(DISTINCT category) FROM courses) as total_categories
    ");
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

    // ===== GET USER'S PERSONAL STATS =====
    if ($user['role'] === 'etudiant') {
        // Student stats
        $userStatsStmt = $pdo->prepare("
            SELECT 
                (SELECT COUNT(*) FROM student_quizzes WHERE student_id = ?) as quizzes_taken,
                (SELECT COUNT(*) FROM student_quizzes sq 
                 WHERE sq.student_id = ? 
                 AND (sq.score * 100.0 / (SELECT COUNT(*) FROM questions WHERE quiz_id = sq.quiz_id)) >= 75) as certificates_earned,
                (SELECT COUNT(*) FROM course_discussions WHERE student_id = ?) as questions_asked,
                (SELECT ROUND(AVG(sq.score * 100.0 / (SELECT COUNT(*) FROM questions WHERE quiz_id = sq.quiz_id)), 1)
                 FROM student_quizzes sq WHERE sq.student_id = ?) as average_score
        ");
        $userStatsStmt->execute([$user_id, $user_id, $user_id, $user_id]);
        $userStats = $userStatsStmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Teacher stats
        $userStatsStmt = $pdo->prepare("
            SELECT 
                (SELECT COUNT(*) FROM courses WHERE teacher_id = ?) as courses_created,
                (SELECT COUNT(*) FROM quizzes q 
                 JOIN courses c ON q.course_id = c.id 
                 WHERE c.teacher_id = ?) as quizzes_created,
                (SELECT COUNT(*) FROM course_discussions cd 
                 JOIN courses c ON cd.course_id = c.id 
                 WHERE c.teacher_id = ?) as questions_received
        ");
        $userStatsStmt->execute([$user_id, $user_id, $user_id]);
        $userStats = $userStatsStmt->fetch(PDO::FETCH_ASSOC);
    }

    // ===== BUILD COMPREHENSIVE CONTEXT =====
    $context = "You are Claude, an AI study assistant for CTH Academy.\n\n";
    
    // User's complete profile
    $context .= "=== STUDENT PROFILE ===\n";
    $context .= "Full Name: {$user['prenom']} {$user['nom']}\n";
    $context .= "Email: {$user['email']}\n";
    $context .= "Role: " . ($user['role'] === 'etudiant' ? 'Student' : 'Teacher') . "\n";
    if ($user['phone']) $context .= "Phone: {$user['phone']}\n";
    if ($user['location']) $context .= "Location: {$user['location']}\n";
    if ($user['bio']) $context .= "Bio: {$user['bio']}\n";
    $context .= "Member since: " . date('F Y', strtotime($user['created_at'])) . "\n\n";

    // Website statistics
    $context .= "=== CTH ACADEMY STATISTICS ===\n";
    $context .= "Total Courses: {$stats['total_courses']}\n";
    $context .= "Total Quizzes: {$stats['total_quizzes']}\n";
    $context .= "Total Students: {$stats['total_students']}\n";
    $context .= "Total Teachers: {$stats['total_teachers']}\n";
    $context .= "Course Categories: {$stats['total_categories']}\n\n";

    // User's personal statistics
    $context .= "=== {$user['prenom']}'S PERSONAL STATS ===\n";
    if ($user['role'] === 'etudiant') {
        $context .= "Quizzes Completed: {$userStats['quizzes_taken']}\n";
        $context .= "Certificates Earned: {$userStats['certificates_earned']}\n";
        $context .= "Questions Asked: {$userStats['questions_asked']}\n";
        $context .= "Average Quiz Score: {$userStats['average_score']}%\n\n";
    } else {
        $context .= "Courses Created: {$userStats['courses_created']}\n";
        $context .= "Quizzes Created: {$userStats['quizzes_created']}\n";
        $context .= "Student Questions Received: {$userStats['questions_received']}\n\n";
    }

    // Get course context with PDF text
    if ($course_id) {
        $courseStmt = $pdo->prepare("
            SELECT title, category, pdf_text, pdf_extracted, course_summary 
            FROM courses 
            WHERE id = ?
        ");
        $courseStmt->execute([$course_id]);
        $course = $courseStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($course) {
            $context .= "=== CURRENT COURSE ===\n";
            $context .= "Title: {$course['title']}\n";
            $context .= "Category: {$course['category']}\n\n";
            
            if ($course['course_summary']) {
                $context .= "Course Summary:\n{$course['course_summary']}\n\n";
            }
            
            if ($course['pdf_extracted'] == 1 && $course['pdf_text']) {
                $pdf_excerpt = substr($course['pdf_text'], 0, 15000);
                $context .= "Course Content (from PDF):\n{$pdf_excerpt}\n\n";
            }
            
            // Get quiz questions for context
            $quizStmt = $pdo->prepare("
                SELECT q.title as quiz_title, ques.question
                FROM quizzes q
                JOIN questions ques ON q.id = ques.quiz_id
                WHERE q.course_id = ?
                LIMIT 10
            ");
            $quizStmt->execute([$course_id]);
            $questions = $quizStmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($questions) > 0) {
                $context .= "Quiz Topics (for reference, don't reveal answers):\n";
                foreach ($questions as $q) {
                    $context .= "- {$q['question']}\n";
                }
                $context .= "\n";
            }
        }
    }
    
    // Add instruction based on message type
    $context .= "=== YOUR TASK ===\n";
    switch ($message_type) {
        case 'hint':
            $context .= "Provide helpful hints without giving complete answers. Guide {$user['prenom']}'s thinking.\n";
            break;
        case 'explanation':
            $context .= "Explain concepts clearly with examples. Use course content when available.\n";
            break;
        case 'practice':
            $context .= "Generate practice questions based on course content.\n";
            break;
        default:
            $context .= "Answer questions helpfully. Be encouraging and personalized.\n";
    }
    
    // Add conversation history
    $historyStmt = $pdo->prepare("
        SELECT message, response 
        FROM ai_chat_history 
        WHERE user_id = ? AND course_id = ?
        ORDER BY created_at DESC 
        LIMIT 3
    ");
    $historyStmt->execute([$user_id, $course_id]);
    $history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($history) > 0) {
        $context .= "\n=== RECENT CONVERSATION ===\n";
        foreach (array_reverse($history) as $item) {
            $context .= "{$user['prenom']}: {$item['message']}\nYou: {$item['response']}\n\n";
        }
    }
    
    // Final prompt
    $full_prompt = $context . "\n{$user['prenom']}: {$message}\n\nYou:";
    
    // Call Gemini API
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $api_key;
    
    $payload = [
        'contents' => [
            ['parts' => [['text' => $full_prompt]]]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 64,
            'topP' => 0.95,
            'maxOutputTokens' => 2048,
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        $error_data = json_decode($response, true);
        $error_message = $error_data['error']['message'] ?? 'API request failed';
        throw new Exception('Gemini API Error: ' . $error_message);
    }

    $response_data = json_decode($response, true);
    $ai_response = $response_data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
    $ai_response = trim($ai_response);

    // Save to database
    $saveStmt = $pdo->prepare("
        INSERT INTO ai_chat_history (user_id, course_id, message, response, message_type) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $saveStmt->execute([$user_id, $course_id, $message, $ai_response, $message_type]);

    echo json_encode([
        'status' => 'success',
        'response' => $ai_response
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
