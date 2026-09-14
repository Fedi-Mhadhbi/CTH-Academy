<?php
session_start();
require_once "../config/db.php";

// Only students can view
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'etudiant'){
    die("Unauthorized access");
}

$student_id = $_SESSION['id']; // FIXED: Use 'id' not 'user_id'
$quiz_id = $_GET['quiz_id'] ?? null;

if(!$quiz_id){
    die("Quiz ID missing");
}

// Fetch student's quiz attempt
$stmt = $pdo->prepare("SELECT score FROM student_quizzes WHERE student_id=? AND quiz_id=?");
$stmt->execute([$student_id, $quiz_id]);
$attempt = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$attempt){
    die("Quiz not attempted");
}

// Get total questions
$totalStmt = $pdo->prepare("SELECT COUNT(*) as total FROM questions WHERE quiz_id=?");
$totalStmt->execute([$quiz_id]);
$totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
$total_questions = $totalResult['total'];

// Check if score meets requirement (>= 75%)
$threshold = 0.75;
$required_score = ceil($total_questions * $threshold);

if($attempt['score'] < $required_score){
    die("Score too low for certificate. You need at least {$required_score} out of {$total_questions}");
}

// Fetch student info
$studentStmt = $pdo->prepare("SELECT nom, prenom FROM users WHERE id=?");
$studentStmt->execute([$student_id]);
$student = $studentStmt->fetch(PDO::FETCH_ASSOC);

// Fetch quiz info
$quizStmt = $pdo->prepare("SELECT title FROM quizzes WHERE id=?");
$quizStmt->execute([$quiz_id]);
$quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);

$percentage = round(($attempt['score'] / $total_questions) * 100);
$studentName = $student['nom'] . ' ' . $student['prenom'];
$quizTitle = $quiz['title'];
$score = $attempt['score'];
$date = date("F d, Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Certificate of Achievement - <?php echo htmlspecialchars($studentName); ?></title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Georgia', 'Times New Roman', serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.certificate-container {
    background: white;
    width: 100%;
    max-width: 900px;
    padding: 60px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    position: relative;
    border: 15px solid #4b7bec;
}

.certificate-container::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 25px;
    right: 25px;
    bottom: 25px;
    border: 3px solid #4b7bec;
    pointer-events: none;
}

.header {
    text-align: center;
    margin-bottom: 30px;
}

.logo {
    font-size: 42px;
    font-weight: bold;
    color: #4b7bec;
    margin-bottom: 10px;
    letter-spacing: 2px;
}

.certificate-title {
    font-size: 38px;
    color: #2c3e50;
    margin-bottom: 20px;
    font-weight: bold;
}

.divider {
    width: 200px;
    height: 3px;
    background: linear-gradient(to right, transparent, #4b7bec, transparent);
    margin: 20px auto;
}

.content {
    text-align: center;
    margin: 40px 0;
}

.intro-text {
    font-size: 18px;
    color: #7f8c8d;
    margin-bottom: 25px;
    font-style: italic;
}

.student-name {
    font-size: 48px;
    color: #2c3e50;
    font-weight: bold;
    margin: 25px 0;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.name-underline {
    width: 400px;
    max-width: 80%;
    height: 2px;
    background: #bdc3c7;
    margin: 15px auto 30px auto;
}

.achievement-text {
    font-size: 19px;
    color: #555;
    line-height: 1.6;
    margin: 20px 0;
}

.quiz-title {
    font-size: 28px;
    color: #4b7bec;
    font-weight: bold;
    margin: 25px 0;
}

.score-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px;
    border-radius: 15px;
    margin: 30px auto;
    max-width: 400px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.score-box .score-label {
    font-size: 18px;
    margin-bottom: 10px;
    opacity: 0.9;
}

.score-box .score-value {
    font-size: 42px;
    font-weight: bold;
    margin: 10px 0;
}

.score-box .percentage {
    font-size: 24px;
    opacity: 0.95;
}

.footer {
    text-align: center;
    margin-top: 50px;
}

.date {
    font-size: 16px;
    color: #7f8c8d;
    font-style: italic;
    margin-bottom: 30px;
}

.signature-line {
    width: 250px;
    height: 2px;
    background: #bdc3c7;
    margin: 30px auto 10px auto;
}

.signature-text {
    font-size: 14px;
    color: #7f8c8d;
}

.academy-footer {
    margin-top: 30px;
    font-size: 15px;
    color: #95a5a6;
}

.print-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #4b7bec;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(75, 123, 236, 0.3);
    transition: all 0.3s;
}

.print-btn:hover {
    background: #3a5fcc;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(75, 123, 236, 0.4);
}

.emoji {
    font-size: 48px;
    margin: 20px 0;
}

@media print {
    body {
        background: white;
    }
    .print-btn {
        display: none;
    }
    .certificate-container {
        box-shadow: none;
        border: 10px solid #4b7bec;
    }
}

@media (max-width: 768px) {
    .certificate-container {
        padding: 30px 20px;
    }
    .certificate-title {
        font-size: 28px;
    }
    .student-name {
        font-size: 32px;
    }
    .quiz-title {
        font-size: 22px;
    }
    .score-box .score-value {
        font-size: 32px;
    }
}
</style>
</head>
<body>

<button class="print-btn" onclick="window.print()">🖨️ Print Certificate</button>

<div class="certificate-container">
    <div class="header">
        <div class="logo">🎓 CTH ACADEMY</div>
        <div class="certificate-title">Certificate of Achievement</div>
        <div class="divider"></div>
    </div>

    <div class="content">
        <div class="intro-text">This is to certify that</div>
        
        <div class="student-name"><?php echo htmlspecialchars($studentName); ?></div>
        <div class="name-underline"></div>

        <div class="achievement-text">
            has successfully completed the quiz
        </div>

        <div class="quiz-title"><?php echo htmlspecialchars($quizTitle); ?></div>

        <div class="emoji">🌟</div>

        <div class="score-box">
            <div class="score-label">Final Score</div>
            <div class="score-value"><?php echo $score; ?> / <?php echo $total_questions; ?></div>
            <div class="percentage"><?php echo $percentage; ?>%</div>
        </div>

        <div class="achievement-text">
            <?php if($percentage >= 90): ?>
                <strong>Outstanding Performance!</strong> 🏆
            <?php elseif($percentage >= 80): ?>
                <strong>Excellent Work!</strong> ⭐
            <?php else: ?>
                <strong>Great Job!</strong> ✨
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <div class="date">Issued on: <?php echo $date; ?></div>
        
        <div class="signature-line"></div>
        <div class="signature-text">CTH Academy Director</div>
        
        <div class="academy-footer">
            CTH Academy - Online Learning Platform<br>
            <small>This certificate verifies successful completion of the quiz</small>
        </div>
    </div>
</div>

</body>
</html>