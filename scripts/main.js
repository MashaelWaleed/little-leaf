const openSearch = document.getElementById("openSearch");
const overlay = document.getElementById("searchOverlay");
const searchBtn = document.getElementById("searchBtn");
const searchInput = document.getElementById("searchInput");

// open overlay
openSearch?.addEventListener("click", (e) => {
  e.preventDefault();
  overlay.classList.add("active");
  searchInput.focus();
});

// close overlay when clicking outside
overlay?.addEventListener("click", (e) => {
  if (e.target === overlay) {
    overlay.classList.remove("active");
  }
});

// search button
searchBtn?.addEventListener("click", () => {
  const query = searchInput.value.trim();

  console.log("Search Query:", query); // Check your F12 Console to see if this triggers

  if (query.length >= 2) {
    // Require at least 2 characters
    const targetURL = `/store/pages/products.php?search=${encodeURIComponent(query)}`;
    console.log("Redirecting to:", targetURL);
    window.location.href = targetURL;
  } else {
    searchInput.value = "";
    searchInput.placeholder = "Please enter a plant name...";
    searchInput.classList.add("shake-error"); // Add a little visual feedback
    setTimeout(() => searchInput.classList.remove("shake-error"), 500);
  }
});

// press Enter to search
searchInput?.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    searchBtn.click();
  }
});

// --- Hamburger Menu Toggle ---
const hamburger = document.getElementById("hamburger");
const navLinks = document.querySelector(".nav-links");

if (hamburger) {
  hamburger.addEventListener("click", () => {
    // Toggles the "X" animation on the icon
    hamburger.classList.toggle("active");
    // Slides the menu in and out
    navLinks.classList.toggle("active");
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const backToTopBtn = document.getElementById("backToTopBtn");
  const scrollContainer = document.querySelector(".scroll-page"); //  your main container

  if (!backToTopBtn || !scrollContainer) {
    return; // Fails silently and safely moves on!
  }

  // listen to container scroll instead of window
  scrollContainer.addEventListener("scroll", () => {
    if (scrollContainer.scrollTop > 600) {
      backToTopBtn.style.display = "flex";
    } else {
      backToTopBtn.style.display = "none";
    }
  });

  // 🔼 scroll back to top of container
  backToTopBtn.addEventListener("click", () => {
    scrollContainer.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });
});
document.getElementById("year").textContent = new Date().getFullYear();

/**
 * Global Toast Trigger
 * @param {string} message - The text to show
 * @param {string} type - 'success' or 'error'
 */
function showGlobalToast(message, type = "success") {
  const toast = document.getElementById("global-toast");
  const msgContainer = document.getElementById("global-toast-msg");

  if (!toast) return; // Safety check

  msgContainer.innerText = message;

  // Reset classes and add new ones
  toast.className = `toast show toast-${type}`;

  // Hide after 4 seconds
  setTimeout(() => {
    toast.classList.remove("show");
  }, 4000);
}

//determine the message and prevent showen when reload
window.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);

  if (urlParams.has("status")) {
    const status = urlParams.get("status");

    // Pick the right message for the Little Leaf vibe
    if (status === "success") {
      showGlobalToast("Your thoughts have been planted! 🌿", "success");
    } else if (status === "sent") {
      showGlobalToast("Your message sent successfully! 🌿", "success");
    } else if (status === "updated") {
      showGlobalToast("Profile updated successfully! 🌿", "success");
    }else if (status === "order") {
      showGlobalToast("Your order recieved successfully! 🌿", "success");
    }


    // --- THE "SCRUBBER" ---
    // This removes the ?status=... from the URL bar immediately
    const cleanURL = window.location.origin + window.location.pathname;
    window.history.replaceState({}, document.title, cleanURL);
  }

  if (urlParams.has("error")) {
    const error = urlParams.get("error");

    // Pick the right message for the Little Leaf vibe
    if (error === "exists") {
      showGlobalToast(
        "This email address is already registered to another account. 🥀",
        "error",
      );
    } else if (error === "error") {
      showGlobalToast("Something went wrong. Try again. 🥀", "error");
    } else if (error === "email_mismatch") {
      showGlobalToast("This is not your email. Try again. 🥀", "error");
    } else if (error === "fail_order") {
      showGlobalToast("The order not recieved. Try again. 🥀", "error");
    }

    // --- THE "SCRUBBER" ---
    // This removes the ?status=... from the URL bar immediately
    const cleanURL = window.location.origin + window.location.pathname;
    window.history.replaceState({}, document.title, cleanURL);
  }
});
