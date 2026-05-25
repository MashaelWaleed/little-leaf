// Add this to your profile.js
const profileForm = document.querySelector(".profile-form");

profileForm?.addEventListener("submit", async (e) => {
  e.preventDefault();

  // Show loading state on button
  const submitBtn = profileForm.querySelector('button[name="update_profile"]');
  const originalText = submitBtn.innerText;
  submitBtn.innerText = "Saving...";
  submitBtn.disabled = true;

  const formData = new FormData(profileForm);
  formData.append("update_profile", "1"); // Ensure the PHP isset() check passes

  try {
    const response = await fetch("../server/update_profile.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.status === "success") {
      showGlobalToast("Profile updated successfully! 🌿", "success");

      // Optional: Update any other UI elements that show the name
      const userNameDisplay = document.querySelector(".user-name-display");
      if (userNameDisplay) {
        userNameDisplay.innerText = `${result.data.fName} ${result.data.lName}`;
      }
    } else {
      // Handle specific errors based on message
      if (result.message === "validation_failed") {
        showGlobalToast(`${result.errors.join(" ")} 🥀`, "error");
      } else if (result.message === "invalid_phone") {
        showGlobalToast(
          "Please enter a valid Saudi mobile number. 🥀",
          "error",
        );
      } else if (result.message === "exists") {
        showGlobalToast("This email is already in use. 🥀", "error");
      } else {
        showGlobalToast("Something went wrong. 🥀", "error");
      }
    }
  } catch (error) {
    console.error("Fetch error:", error);
    showGlobalToast("Server connection error. 🥀", "error");
  } finally {
    // Reset button state
    submitBtn.innerText = originalText;
    submitBtn.disabled = false;
  }
});

// and Select the element that has the overflow-y: scroll
const scrollContainer = document.querySelector(".container");
// 1. Target the .nav-link directly
const sections = document.querySelectorAll(".profile-section");
const profileNavLinks = document.querySelectorAll(".sidebar-links .nav-link");

const observerOptions = {
  // THIS IS THE KEY: We set the scrollable container as the root
  root: scrollContainer,

  // Adjusted margins to trigger better within a container
  rootMargin: "-10% 0px -60% 0px",
  threshold: 0.1,
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      const id = entry.target.getAttribute("id");

      profileNavLinks.forEach((link) => {
        // Toggle the active class based on the matching ID
        const isMatch = link.getAttribute("href") === `#${id}`;
        link.classList.toggle("active", isMatch);
      });
    }
  });
}, observerOptions);

sections.forEach((section) => observer.observe(section));

//delete button---------------
async function deleteAddress(addressId) {
  if (!confirm("Are you sure you want to delete this address?")) return;

  // Create a virtual form
  const formData = new FormData();
  formData.append("action", "delete");
  formData.append("address_id", addressId);

  try {
    const response = await fetch("../server/address_process.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();

    if (result.status === "success") {
      // 🆕 DYNAMIC UPDATE: Instead of location.reload()
      // Find the button that was clicked, then find its parent .info-card
      const cardToDelete = document
        .querySelector(`button[onclick*="deleteAddress(${addressId})"]`)
        .closest(".info-card");

      // Add a fade-out effect (optional but professional)
      cardToDelete.style.opacity = "0";
      setTimeout(() => {
        cardToDelete.remove(); // Remove from DOM entirely
        showGlobalToast("Address removed! 🌿", "success");
      }, 300);
    }
  } catch (error) {
    console.error("Fetch error:", error);
    showGlobalToast("Server connection error. 🥀", "error");
  }
}

// Function to open modal for ADDING
function openAddModal() {
  document.getElementById("address-form").reset();
  document.getElementById("address-id").value = "";
  document.getElementById("form-action").value = "add";
  document.getElementById("modal-title").innerText = "Add New Address";
  // Default the checkbox to unchecked for new addresses
  document.getElementById("field-default").checked = false;
  document.getElementById("address-modal").style.display = "flex";
}

// Function to open modal for EDITING
// You pass the data object from your PHP loop to this function
function openEditModal(data) {
  document.getElementById("address-id").value = data.id;
  document.getElementById("form-action").value = "edit";
  document.getElementById("modal-title").innerText = "Edit Address";
  // Fill the fields
  document.getElementById("field-label").value = data.label;
  document.getElementById("field-name").value = data.full_name;
  document.getElementById("field-address").value = data.address_line;
  document.getElementById("field-city").value = data.city;
  document.getElementById("field-province").value = data.province;
  // Handle the checkbox
  // data.is_default will be 1 or 0 (or '1'/'0' as a string)
  document.getElementById("field-default").checked = data.is_default == 1; //just read the value from db
  // If it's already default, don't let them uncheck it!
  // They must set another address as default to move the 'Default' status.
  if (data.is_default == 1) {
    document.getElementById("field-default").disabled = true;
  } else {
    document.getElementById("field-default").disabled = false;
  }

  document.getElementById("address-modal").style.display = "flex";
}

function closeModal() {
  document.getElementById("address-modal").style.display = "none";
}

// Handle Address Form Submission (ADD & EDIT)
document
  .getElementById("address-form")
  .addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const action = formData.get("action");

    try {
      const response = await fetch("../server/address_process.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.status === "success") {
        if (action === "add") {
          // Dynamic Add: Insert new card before the "Add New" placeholder
          const newCardHTML = createAddressCardHTML(result.data);
          document
            .querySelector("#saved-addresses .add-new-card")
            .insertAdjacentHTML("beforebegin", newCardHTML);
          showGlobalToast("New sanctuary added! 🌿", "success");
        } else {
          // Dynamic Update: Refresh content of the specific card
          const card = document
            .querySelector(
              `button[onclick*="deleteAddress(${formData.get("address_id")})"]`,
            )
            .closest(".info-card");
          card.querySelector("h4").innerText = formData.get("label");
          card.querySelector("p").innerHTML = `
                    ${formData.get("full_name")}<br />
                    ${formData.get("address_line")}<br />
                    ${formData.get("city")}, ${formData.get("province")}<br />
                    Saudi Arabia
                `;
          showGlobalToast("Address updated! 🌿", "success");
        }
        closeModal();
      } else if (result.status === "validation_failed") {
        // Join array items with a break to present errors cleanly inside a toast
        const alertText = result.errors.join("\n");
        showToast(alertText, "error");
      } else {
        showToast("Failed to complete action.", "error");
      }
    } catch (error) {
      console.error("Fetch error:", error);
      showGlobalToast("Server connection error. 🥀", "error");
    }
  });
// --- Payment Methods Handling ---
function openPaymentModal() {
  document.getElementById("payment-form").reset();
  document.getElementById("payment-modal").style.display = "flex";
}
function closePaymentModal() {
  document.getElementById("payment-modal").style.display = "none";
}
document
  .getElementById("payment-form")
  .addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const fullCardNum = document.getElementById("card-num-input").value;
    const brand = fullCardNum.startsWith("5") ? "Mastercard" : "Visa";

    formData.append("card_brand", brand);
    formData.append("last4", fullCardNum.slice(-4));

    try {
      const response = await fetch("../server/address_process.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.status === "success") {
        const newCardHTML = createPaymentCardHTML(result.data);
        document
          .querySelector("#payment-methods .add-new-card")
          .insertAdjacentHTML("beforebegin", newCardHTML);
        closePaymentModal();
        showGlobalToast("Payment method saved! 💳", "success");
      } else if (result.status === "validation_failed") {
        const alertText = result.errors.join("\n");
        showToast(alertText, "error");
      }
    } catch (error) {
      showGlobalToast("Error saving card. 🥀", "error");
    }
  });

async function deleteCard(cardId) {
  if (!confirm("Are you sure you want to delete this card?")) return;

  const formData = new FormData();
  formData.append("action", "deleteCard");
  formData.append("card_id", cardId);

  try {
    const response = await fetch("../server/address_process.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();

    if (result.status === "success") {
      const cardElement = document
        .querySelector(`button[onclick*="deleteCard(${cardId})"]`)
        .closest(".info-card");
      cardElement.style.opacity = "0";
      cardElement.style.transform = "scale(0.9)";
      setTimeout(() => {
        cardElement.remove();
        showGlobalToast("Card removed. 🥀", "success");
      }, 300);
    }
  } catch (error) {
    showGlobalToast("Server connection error. 🥀", "error");
  }
}

// --- HTML Generators (For AJAX injection) ---

function createAddressCardHTML(data) {
  return `
        <div class="info-card">
            ${data.is_default == 1 ? '<span class="badge">Default</span>' : ""}
            <h4>${data.label}</h4>
            <p>
                ${data.full_name}<br />
                ${data.address_line}<br />
                ${data.city}, ${data.province}<br />
                Saudi Arabia
            </p>
            <div class="card-actions">
                <button class="text-btn" onclick='openEditModal(${JSON.stringify(data)})'>Edit</button>
                <button class="text-btn danger" onclick="deleteAddress(${data.id})">Delete</button>
            </div>
        </div>`;
}

function createPaymentCardHTML(data) {
  return `
        <div class="info-card">
            ${data.is_default == 1 ? '<span class="badge">Default</span>' : ""}
            <h4>${data.card_brand} ending in ${data.last4}</h4>
            <p>Expires ${data.expiry_month} / ${data.expiry_year}</p>
            <p class="card-name">${data.cardholder_name}</p>
            <div class="card-actions">
                <button class="text-btn danger" onclick="deleteCard(${data.id})">Remove</button>
            </div>
        </div>`;
}
// --- Input Formatting for Card Number ---
const cardInput = document.getElementById("card-num-input");
if (cardInput) {
  cardInput.addEventListener("input", function (e) {
    // Clear all non-digits
    let value = e.target.value.replace(/\D/g, "");
    // Group by 4 digits
    let formattedValue = value.match(/.{1,4}/g)?.join(" ") || "";
    e.target.value = formattedValue;
  });
}
