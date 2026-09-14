<?php
session_start();
require_once '../config/db.php';

// Only teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'enseignant') {
    header("Location: ../../login.html");
    exit;
}

// Fetch courses dynamically (based on category)
$categories = ['Web Development','Programming','AI'];
$in  = str_repeat('?,', count($categories) - 1) . '?';
$stmt = $pdo->prepare("SELECT id, title, category FROM courses WHERE category IN ($in) ORDER BY category, title");
$stmt->execute($categories);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CTH Academy | Create Quiz</title>
<link rel="stylesheet" href="css/style.css">
<style>
body { font-family: Arial, sans-serif; background: #f4f6f9; margin:0; }
nav { background: #4b7bec; color: #fff; padding: 15px; display:flex; justify-content:space-between; }
nav a { color:#fff; text-decoration:none; font-weight:bold; margin-left:15px; }
.create-quiz { max-width:800px; margin:30px auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.create-quiz h2 { text-align:center; margin-bottom:20px; }
.create-quiz label { display:block; margin-top:15px; }
.create-quiz input, .create-quiz select { width:100%; padding:8px; margin-top:5px; border-radius:6px; border:1px solid #ccc; }
.question { background:#f9f9f9; padding:15px; margin-top:15px; border-radius:8px; border:1px solid #ddd; }
button { margin-top:15px; padding:10px 20px; border:none; border-radius:8px; background:#4b7bec; color:#fff; cursor:pointer; font-weight:bold; }
button:hover { background:#3a5fcc; }
#message { margin-top:15px; font-weight:bold; }
</style>
</head>
<body>

<nav>
  <div class="logo">CTH Academy</div>
  <div>
    <a href="/projet/enseignant.html">Dashboard</a>
     <a href="../auth/logout.php">Logout</a>
  </div>
</nav>

<section class="create-quiz">
  <h2>Create New Quiz</h2>

  <form id="quizForm">
    <label for="course">Select Course:</label>
    <select id="course" required>
      <option value="">-- Choose Course --</option>
      <?php foreach($courses as $c): ?>
        <option value="<?= $c['id'] ?>">
          [<?= $c['category'] ?>] <?= htmlspecialchars($c['title']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label for="quizTitle">Quiz Title:</label>
    <input type="text" id="quizTitle" placeholder="Enter quiz title" required>

    <div id="questionsContainer">
      <!-- Questions will appear here -->
    </div>

    <button type="button" id="addQuestionBtn">+ Add Question</button>
    <button type="submit">Create Quiz</button>
  </form>

  <p id="message"></p>
</section>

<script>
// Question counter
let questionCount = 0;
const questionsContainer = document.getElementById('questionsContainer');
const addQuestionBtn = document.getElementById('addQuestionBtn');

// Add new question
addQuestionBtn.addEventListener('click', () => {
    questionCount++;
    const div = document.createElement('div');
    div.classList.add('question');
    div.innerHTML = `
        <h4>Question ${questionCount}</h4>
        <input type="text" placeholder="Question text" class="qText" required>
        <input type="text" placeholder="Option 1" class="opt1" required>
        <input type="text" placeholder="Option 2" class="opt2" required>
        <input type="text" placeholder="Option 3" class="opt3" required>
        <input type="text" placeholder="Option 4" class="opt4" required>
        <input type="number" placeholder="Correct Option (1-4)" class="correct" min="1" max="4" required>
    `;
    questionsContainer.appendChild(div);
});

// Submit quiz
document.getElementById('quizForm').addEventListener('submit', function(e){
    e.preventDefault();
    const course_id = document.getElementById('course').value;
    const title = document.getElementById('quizTitle').value;
    const questions = [];

    document.querySelectorAll('.question').forEach(qDiv => {
        questions.push({
            question: qDiv.querySelector('.qText').value,
            option1: qDiv.querySelector('.opt1').value,
            option2: qDiv.querySelector('.opt2').value,
            option3: qDiv.querySelector('.opt3').value,
            option4: qDiv.querySelector('.opt4').value,
            correct_option: qDiv.querySelector('.correct').value
        });
    });

    fetch('create_quiz_submit.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({course_id, title, questions})
    })
    .then(res => res.json())
    .then(data => {
        console.log(data);
        document.getElementById('message').textContent = data.message;
        if(data.status === 'success'){
            document.getElementById('quizForm').reset();
            questionsContainer.innerHTML = '';
            questionCount = 0;
        }
    })
    .catch(err => {
        console.error(err);
        document.getElementById('message').textContent = 'An error occurred. Try again.';
    });
});

function logout(){
    window.location.href = '../logout.php';
}
</script>

</body>
</html>