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

//delete button--------------------------------------------------------
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
      // Remove the card from the screen without reloading!
      location.reload();
    }
  } catch (error) {
    console.error("Fetch error:", error);
    showGlobalToast("Server connection error. 🥀", "error");
  }
}

//add or update button-------------------------------------------------
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

// Handle Form Submission via AJAX----------------------------------
document
  .getElementById("address-form")
  .addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const response = await fetch("../server/address_process.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      // 1. Logic for SUCCESS
      if ((result.status = "success")) {
        location.reload();
      }
      // 2. Logic for ERRORS
      else {
        showGlobalToast("Something went wrong. Please try again. 🥀", "error");
      }
    } catch (error) {
      console.error("Fetch error:", error);
      showGlobalToast("Server connection error. 🥀", "error");
      closeModal();
    }
  });

//payment model-------------------------------------------------------------------
function openPaymentModal() {
  document.getElementById("payment-form").reset();
  document.getElementById("payment-modal").style.display = "flex";
}
function closePaymentModal() {
  document.getElementById("payment-modal").style.display = "none";
}
// Handle Form Submission via AJAX----------------------------------
document
  .getElementById("payment-form")
  .addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const fullCardNum = document.getElementById("card-num-input").value;

    // Detect Brand (Simple logic)
    let brand = "Visa";
    if (fullCardNum.startsWith("5")) brand = "Mastercard";

    // Only send the SAFE data to the server(append info to formdata)
    formData.append("card_brand", brand);
    formData.append("last4", fullCardNum.slice(-4));

    try {
      const response = await fetch("../server/address_process.php", {
        // You can use the same controller!
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.status === "success") {
        location.reload();
      } else {
        showGlobalToast("Something went wrong. Please try again. 🥀", "error");
      }
    } catch (error) {
      console.error("Fetch error:", error);
      showGlobalToast("Failed to save card.", "error");
      closePaymentModal();
    }
  });

  //delete card------------------------------------------------------------------
  //delete button--------------------------------------------------------
async function deleteCard(cardId) {
  if (!confirm("Are you sure you want to delete this card?")) return;

  // Create a virtual form
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
      // Remove the card from the screen without reloading!
      location.reload();
    }
  } catch (error) {
    console.error("Fetch error:", error);
    showGlobalToast("Server connection error. 🥀", "error");
  }
}
