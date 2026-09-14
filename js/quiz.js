const questions = [
  {q:"HTML est un langage de ?", a:["Balisage","Programmation","Base de données","Design"], correct:0},
  {q:"La balise pour un paragraphe ?", a:["<p>","<h1>","<div>","<span>"], correct:0},
  {q:"CSS signifie ?", a:["Cascading Style Sheets","Computer Style Sheets","Creative Style Sheets","Color Style Sheets"], correct:0},
  {q:"Pour changer la couleur du texte en CSS ?", a:["color","background","font","text-color"], correct:0},
  {q:"La méthode JavaScript pour afficher un message ?", a:["alert()","console.log()","write()","prompt()"], correct:0},
  {q:"Sélecteur CSS pour un id ?", a:["#id",".id","id","*id"], correct:0},
  {q:"Sélecteur CSS pour une classe ?", a:[".class","#class","class","*class"], correct:0},
  {q:"Pour créer un lien en HTML ?", a:["<a>","<link>","<href>","<nav>"], correct:0},
  {q:"Attribut pour image src ?", a:["src","href","img","source"], correct:0},
  {q:"Balise HTML pour liste non ordonnée ?", a:["<ul>","<ol>","<li>","<list>"], correct:0},
  {q:"Pour ajouter un commentaire en HTML ?", a:["<!-- commentaire -->","// commentaire","/* commentaire */","** commentaire **"], correct:0},
  {q:"Balise pour titre niveau 1 ?", a:["<h1>","<h2>","<header>","<title>"], correct:0},
  {q:"Pour centrer un texte en CSS ?", a:["text-align:center","align:center","center:text","text:center"], correct:0},
  {q:"Propriété CSS pour marge ?", a:["margin","padding","spacing","border"], correct:0},
  {q:"Méthode JS pour obtenir valeur input ?", a:["document.getElementById('id').value","document.getElement('id')","getValue()","input.value"], correct:0},
  {q:"Pour déclarer une variable en JS ?", a:["let x","var x","const x","كل الخيارات صحيحة"], correct:3},
  {q:"Boucle for en JS commence par ?", a:["for(let i=0;i<10;i++)","for i in range","while(i<10)","loop i=0"], correct:0},
  {q:"Evénement JS pour clic ?", a:["onclick","onhover","onchange","onload"], correct:0},
  {q:"Balise meta charset ?", a:["<meta charset='UTF-8'>","<meta utf-8>","<meta encoding='utf-8'>","<charset>"], correct:0},
  {q:"CSS inline s'écrit comment ?", a:["<p style='color:red'>","<p css='color:red'>","<p class='color:red'>","<p id='color:red'>"], correct:0},
];

// shuffle et choisir 20 questions
let shuffled = questions.sort(() => 0.5 - Math.random()).slice(0,20);

let current = 0;
let score = 0;

const questionEl = document.getElementById("question");
const answersEl = document.getElementById("answers");
const nextBtn = document.getElementById("nextBtn");
const scoreEl = document.getElementById("score");

function loadQuestion() {
  let q = shuffled[current];
  questionEl.textContent = q.q;
  answersEl.innerHTML = "";
  nextBtn.style.display = "none";

  q.a.forEach((answer,i)=>{
    const btn = document.createElement("button");
    btn.textContent = answer;
    btn.onclick = ()=>selectAnswer(i,btn);
    answersEl.appendChild(btn);
  });
}

function selectAnswer(i,btn){
  const correctIndex = shuffled[current].correct;
  Array.from(answersEl.children).forEach(b=>{
    b.disabled = true;
  });

  if(i === correctIndex){
    btn.classList.add("correct");
    score++;
  } else {
    btn.classList.add("wrong");
    answersEl.children[correctIndex].classList.add("correct");
  }

  nextBtn.style.display = "inline-block";
}

// bouton suivant
nextBtn.addEventListener("click", ()=>{
  current++;
  if(current < shuffled.length){
    loadQuestion();
  } else {
    showScore();
  }
});

function showScore(){
  questionEl.textContent = "Quiz terminé !";
  answersEl.innerHTML = "";
  nextBtn.style.display = "none";
  scoreEl.textContent = `Votre score : ${score} / ${shuffled.length}`;
}

// initialiser quiz
loadQuestion();
function showWebCourses() {
  const coursesSection = document.getElementById("courses");
  coursesSection.style.display = "block";
  coursesSection.scrollIntoView({ behavior: "smooth" });
}
