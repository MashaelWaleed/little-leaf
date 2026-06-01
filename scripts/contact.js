document.addEventListener("DOMContentLoaded", () => {
  const contactForm = document.getElementById("contactForm");
  const submitBtn = document.getElementById("submitBtn");
  const inputs = contactForm.querySelectorAll("input, textarea");

  // isEnabled will be true only if all required fields pass their HTML5 checks
  const checkFormValidity = () => {
    const isEnabled = contactForm.checkValidity();
    submitBtn.disabled = !isEnabled;
  };
  // Listen for typing events on all inputs
  inputs.forEach((input) => {
    input.addEventListener("input", checkFormValidity);
  });

  // Initial check
  checkFormValidity();

  // --- AJAX Implementation using showGlobalToast ---
  // Create a container dynamically for showing feedback status messages

  contactForm.addEventListener("submit", function (e) {
    e.preventDefault(); // Stop standard full-page reload

    // Change button state to visual loading mode
    submitBtn.disabled = true;
    submitBtn.innerText = "Sending...";

    // Gather all the form data
    const formData = new FormData(contactForm);
    // Explicitly append the submit button token so PHP isset($_POST['contact-submit']) matches
    formData.append("contact-submit", "1");

    // Send data asynchronously via fetch API
    fetch(contactForm.getAttribute("action"), {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response failure.");
        }
        return response.json(); // Parse the JSON data sent back from PHP
      })
      .then((data) => {
        if (data.status === "success") {
          // Trigger your custom success toast!
          showGlobalToast(data.message, "success");

          contactForm.reset(); // Clear all form input values
          checkFormValidity(); // Re-disable the submit button since fields are empty now
        } else {
          // Trigger your custom error toast for server-side validation issues
          showGlobalToast(data.message, "error");
          submitBtn.disabled = false;
        }
      })
      .catch((error) => {
        // Trigger your custom error toast for complete connection failures
        showGlobalToast(
          "Could not reach server. Please check your internet connection.",
          "error",
        );
        submitBtn.disabled = false;
      })
      .finally(() => {
        // Restore button visual text status once request completes
        if (submitBtn.innerText === "Sending...") {
          submitBtn.innerText = "Send Message";
        }
      });
  });
});
