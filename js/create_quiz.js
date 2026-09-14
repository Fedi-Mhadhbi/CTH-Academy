let questionCount = 0;
const questionsContainer = document.getElementById('questionsContainer');
document.getElementById('addQuestionBtn').addEventListener('click', () => {
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

    fetch('create_quiz.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({course_id, title, questions})
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('message').textContent = data.message;
        if(data.status === 'success'){
            document.getElementById('quizForm').reset();
            questionsContainer.innerHTML = '';
            questionCount = 0;
        }
    });
});