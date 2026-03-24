document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");

  const showRegister = document.getElementById("showRegister");
  const showLogin = document.getElementById("showLogin");

  // switch to register
  showRegister.addEventListener("click", (e) => {
    e.preventDefault();
    loginForm.classList.remove("active-form");
    registerForm.classList.add("active-form");
  });

  // switch back to login
  showLogin.addEventListener("click", (e) => {
    e.preventDefault();
    registerForm.classList.remove("active-form");
    loginForm.classList.add("active-form");
  });
});

// loginForm.addEventListener("submit", (e) => {
//   e.preventDefault();

//   const email = document.getElementById("email").value;
//   const password = document.getElementById("password").value;

//   // simulate login validation
//   if (email && password) {
//     localStorage.setItem("isLoggedIn", "true");
//     localStorage.setItem("userEmail", email);

//     // redirect to profile
//     window.location.href = "../index.html";
//   }
// });
