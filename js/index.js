const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const strengthBar = document.getElementById("strengthBar");
const strengthText = document.getElementById("strengthText");
const form = document.getElementById("signupForm");
const error = document.getElementById("error");
const nom = document.getElementById("nom");
const phone = document.getElementById("phone");
const roleSelect = document.getElementById("role"); // nouveau

// --- Password Strength ---
password.addEventListener("input", () => {
  const value = password.value;

  const startsWithUppercase = /^[A-Z]/.test(value);
  const hasNumber = /[0-9]/.test(value);
  const hasSpecialChar = /[&.,;!:?]/.test(value);
  const hasMinLength = value.length >= 6;

  let strength = 0;
  if (startsWithUppercase) strength++;
  if (hasNumber) strength++;
  if (hasSpecialChar) strength++;
  if (hasMinLength) strength++;

  if (strength <= 1) {
    strengthBar.style.width = "30%";
    strengthBar.style.background = "red";
    strengthText.textContent = "Mot de passe faible";
  } else if (strength === 2 || strength === 3) {
    strengthBar.style.width = "60%";
    strengthBar.style.background = "orange";
    strengthText.textContent = "Mot de passe moyen";
  } else {
    strengthBar.style.width = "100%";
    strengthBar.style.background = "green";
    strengthText.textContent = "Mot de passe fort";
  }
});

// --- Form Submit ---
form.addEventListener("submit", (e) => {
  e.preventDefault();
  error.textContent = "";

  // Nom validation
  if(nom.value.length > 15){
    error.textContent = "Le nom ne doit pas dépasser 15 caractères.";
    return;
  }

  // Phone validation
  if(!/^[592][0-9]{7,8}$/.test(phone.value)){
    error.textContent = "Le numéro doit commencer par 5, 9 ou 2 et contenir 8  chiffres.";
    return;
  }

  // Password validation
 const value = password.value;

const startsWithUppercase = /^[A-Z]/.test(value);
const hasNumber = /[0-9]/.test(value);
const hasSpecialChar = /[&.,;!:?]/.test(value);
const hasMinLength = value.length >= 6;

if (!startsWithUppercase) {
  error.textContent = "Le mot de passe doit commencer par une majuscule.";
  return;
}

if (!hasNumber) {
  error.textContent = "Le mot de passe doit contenir au moins un chiffre.";
  return;
}

if (!hasSpecialChar) {
  error.textContent = "Le mot de passe doit contenir un caractère spécial (& . , ; ! : ?).";
  return;
}

if (!hasMinLength) {
  error.textContent = "Le mot de passe doit contenir au moins 6 caractères.";
  return;
}

if (value !== confirmPassword.value) {
  error.textContent = "Les mots de passe ne correspondent pas.";
  return;
}


  // Role validation
  const role = roleSelect.value;
  if (!role) {
    error.textContent = "Veuillez sélectionner votre rôle.";
    return;
  }

  // --- Redirection selon rôle ---
  if(role === "etudiant"){
    window.location.href = "etudiant.html";
  } else if(role === "enseignant"){
    window.location.href = "enseignant.html";
  }

  alert("Compte créé avec succès ");
});
