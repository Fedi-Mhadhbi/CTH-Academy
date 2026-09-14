console.log("auth.js loaded");


document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  console.log("loginForm =", loginForm);
});
// ------------- SIGNUP -------------
const signupForm = document.getElementById("signupForm");

if (signupForm) {
  signupForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const data = {
      nom: document.getElementById("nom").value.trim(),
      prenom: document.getElementById("prenom").value.trim(),
      email: document.getElementById("email").value.trim(),
      password: document.getElementById("password").value,
      role: document.getElementById("role").value
    };

    if (!data.nom || !data.prenom || !data.email || !data.password || !data.role) {
      alert("All fields are required");
      return;
    }

    try {
      const response = await fetch("backend/auth/register.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      });

      const result = await response.json();
      alert(result.message);
      if (result.status === "success") signupForm.reset();

    } catch (err) {
      console.error("Signup error:", err);
      alert("Server error during signup");
    }
  });
}

// ------------- LOGIN -------------
document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  console.log("loginForm =", loginForm);

  if (!loginForm) return;

  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    console.log("Login clicked");

    const data = {
      email: document.getElementById("loginEmail").value.trim(),
      password: document.getElementById("loginPassword").value
    };

    try {
      const response = await fetch("backend/auth/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      });

      const result = await response.json();
      console.log("SERVER RESPONSE:", result);

      if (result.status === "success") {
        if (result.role === "etudiant") {
          window.location.href = "etudiant.html";
        } else if (result.role === "enseignant") {
          window.location.href = "enseignant.html";
        } else if (result.role === "admin") {
          window.location.href = "/backend/admin/admin_dashboard.php";
        }
      } else {
        document.getElementById("error").innerText = result.message;
      }

    } catch (err) {
      console.error("Fetch error:", err);
      document.getElementById("error").innerText = "Server error";
    }
  });
});