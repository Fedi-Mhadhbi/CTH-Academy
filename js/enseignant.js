/* =======================
   AJOUT DES COURS (PDF)
======================= */

function addPDF() {
  const fileInput = document.getElementById("newPDF");
  const file = fileInput.files[0];

  if (!file) {
    alert("Veuillez sélectionner un fichier PDF");
    return;
  }

  const url = URL.createObjectURL(file);
  const container = document.getElementById("teacherCourses");

  const card = document.createElement("div");
  card.className = "card";

  card.innerHTML = `
    <h3>${file.name}</h3>
    <a href="${url}" target="_blank">📄 Voir le PDF</a>
    <span class="status">Ajouté</span>
  `;

  container.appendChild(card);
  fileInput.value = "";
}

/* =======================
   AJOUT DES QUIZ
======================= */

const questions = [];

function addQuestion() {
  const q = document.getElementById("questionInput").value;
  const a1 = document.getElementById("a1").value;
  const a2 = document.getElementById("a2").value;
  const a3 = document.getElementById("a3").value;
  const a4 = document.getElementById("a4").value;
  const correct = document.getElementById("correct").value;

  if (!q || !a1 || !a2 || !a3 || !a4 || correct === "") {
    alert("Veuillez remplir tous les champs");
    return;
  }

  const question = {
    q: q,
    a: [a1, a2, a3, a4],
    correct: parseInt(correct)
  };

  questions.push(question);
  displayQuestions();

  document.getElementById("questionInput").value = "";
  document.getElementById("a1").value = "";
  document.getElementById("a2").value = "";
  document.getElementById("a3").value = "";
  document.getElementById("a4").value = "";
  document.getElementById("correct").value = "";

  alert("Question ajoutée !");
}

function displayQuestions() {
  const preview = document.getElementById("quizPreview");
  preview.innerHTML = "<h3>Questions ajoutées :</h3>";

  questions.forEach((q, index) => {
    preview.innerHTML += `
      <p><strong>${index + 1}. ${q.q}</strong></p>
      <ul>
        ${q.a.map((ans, i) =>
          `<li ${i === q.correct ? 'style="color:green"' : ''}>${ans}</li>`
        ).join("")}
      </ul>
    `;
  });
}
function loadStudentScores() {
  fetch('backend/quiz/get_teacher_student_scores.php')
    .then(res => res.json())
    .then(data => {
      const table = document.getElementById("studentScoresTable");
      table.innerHTML = '';
      table.innerHTML = data.map((item, index) => `
        <tr>
          <td>${index + 1}</td>
          <td>${item.student_name}</td>
          <td>${item.course_title}</td>
          <td>${item.quiz_title}</td>
          <td>${item.score}</td>
          <td>${item.completed ? "Completed" : "Pending"}</td>
        </tr>
      `).join('');

      // Update total students
      const uniqueStudents = [...new Set(data.map(s => s.student_name))];
      document.getElementById("totalStudents").textContent = uniqueStudents.length;
    })
    .catch(err => console.error(err));
}