const contactForm = document.getElementById("contactForm");
const contactMsg = document.getElementById("contactMsg");

contactForm.addEventListener("submit", function (e) {
  e.preventDefault();

  contactMsg.textContent = "Message sent successfully (demo frontend).";
  contactMsg.style.color = "green";

  contactForm.reset();
});
