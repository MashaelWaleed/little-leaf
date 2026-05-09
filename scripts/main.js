const openSearch = document.getElementById("openSearch");
const overlay = document.getElementById("searchOverlay");
const closeBtn = document.getElementById("closeBtn");
const searchBtn = document.getElementById("searchBtn");
const searchInput = document.getElementById("searchInput");

// open overlay
openSearch?.addEventListener("click", (e) => {
  e.preventDefault();
  //if not in product page go there
  if (window.location.pathname != "/store/pages/products.php") {
    window.location.href = "/store/pages/products.php?search=open";
    return; // stop execution
  }
  overlay.classList.add("active");
  searchInput.focus();
});

// close overlay when clicking outside
overlay?.addEventListener("click", (e) => {
  if (e.target === overlay) {
    overlay.classList.remove("active");
  }
});

// close overlay when clicking closeBtn
closeBtn?.addEventListener("click", (e) => {
  e.preventDefault();
  overlay.classList.remove("active");
});

const resultsContainer = document.querySelector(".product-grid"); // Target the grid on products.php

// A unified function that reads ALL current filter states from the DOM
const performSearch = async () => {
  // 1. Always read the specific values from their IDs
  const query = document.getElementById("searchInput")?.value.trim() || "";
  const category = document.getElementById("categoryFilter")?.value || "";
  const price = document.getElementById("priceFilter")?.value || "500";

  // 2. Update the UI price label instantly (Live feedback)
  const priceDisplay = document.getElementById("priceVal");
  if (priceDisplay) priceDisplay.innerText = price;

  // 3. Construct the URL with all 3 parameters
  // We remove the "length >= 1" check so filters work even if search is empty
  const url = `../server/search.php?q=${encodeURIComponent(query)}&category=${category}&price=${price}`;

  try {
    const response = await fetch(url);
    const data = await response.json();
    updateUI(data); // Refresh your product grid
  } catch (error) {
    console.error("Live search error:", error);
  }
};

// --- Listeners: These trigger the function above ---
// "input" makes the search box and price slider LIVE (every keystroke/pixel)
// "change" is better for the dropdown menu
document
  .getElementById("searchInput")
  ?.addEventListener("input", performSearch);
document
  .getElementById("categoryFilter")
  ?.addEventListener("change", performSearch);
document
  .getElementById("priceFilter")
  ?.addEventListener("input", performSearch);

function updateUI(plants) {
  resultsContainer.innerHTML = ""; // Clear current plants

  if (plants.length === 0) {
    resultsContainer.innerHTML = "<div class='noMatch'></div><div class='noMatch'> <p> No botanical companions found. 🥀</p> </div> <div class='noMatch'></div";
    return;
  }

  // Loop through JSON and build new HTML cards
  plants.forEach((plant) => {
    const card = `
            <div class="product-card">
                <div class="product-image-bg">
                    <img src="../images/products/${plant.image}" alt="${plant.name}" />
                    <button class="add-to-cart-btn" data-id="${plant.id}">+</button>
                </div>
                <div class="product-info">
                    <h3>${plant.name}</h3>
                    <p class="price">${plant.price} SAR</p>
                </div>
            </div>`;
    resultsContainer.insertAdjacentHTML("beforeend", card);
  });
}

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

function showGlobalToast(message, type = "success") {
  // 1. Get the container (ensure <div class="toast-container"></div> exists in your HTML)
  const container = document.querySelector(".toast-container");
  if (!container) return;

  // 2. Create a fresh toast element
  const toast = document.createElement("div");
  toast.className = `toast show toast-${type}`;
  toast.innerHTML = `<span>${message}</span>`;

  // 3. Add it to the container
  container.appendChild(toast);

  // 4. Remove it after 4 seconds with a smooth exit
  setTimeout(() => {
    toast.style.transform = "translateX(120%)"; // Slide out
    setTimeout(() => toast.remove(), 400); // Remove from DOM after animation
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
    } else if (status === "order") {
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
