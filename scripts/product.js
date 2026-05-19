const closeBtn = document.getElementById("closeBtn");
const searchBtn = document.getElementById("searchBtn");
const searchInput = document.getElementById("searchInput");
const overlay = document.getElementById("searchOverlay");

window.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);

  // Check if URL contains ?search=open
  if (params.get("search") === "open") {
    // Make sure the elements exist before using them
    if (overlay && searchInput) {
      // Wait a tiny moment to ensure all scripts and included HTML are ready
      setTimeout(() => {
        overlay.classList.add("active");
        searchInput.focus();
        // Remove ?search=open from the URL without reloading the page
        window.history.replaceState(
          {},
          document.title,
          window.location.pathname,
        );
      }, 100);
    }
  }
});

//open overlay when clicking the search icon
openSearch?.addEventListener("click", (e) => {
  if (overlay && searchInput) {
    overlay.classList.add("active");
    searchInput.focus();
  }
});

// close overlay when clicking outside
overlay?.addEventListener("click", (e) => {
  if (e.target === overlay) {
    overlay.classList.remove("active");
  }
});

// close overlay when clicking the close button
closeBtn?.addEventListener("click", () => {
  overlay.classList.remove("active");
});

const resultsContainer = document.querySelector(".cards-container"); // Target the grid on products.php

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
    resultsContainer.innerHTML =
      "<div class='noMatch'></div><div class='noMatch'> <p> No botanical companions found. 🥀</p> </div> <div class='noMatch'></div";
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

// Remove the old document.querySelectorAll block and replace it with this:

// 🛒 Permanent Event Delegation for Add to Cart
resultsContainer?.addEventListener("click", (e) => {
  // Check if the clicked element (or its inside) is the add-to-cart button
  const button = e.target.closest(".add-to-cart-btn");
  if (!button) return; // If they clicked somewhere else in the card, stop.

  e.preventDefault();
  const plantId = button.getAttribute("data-id");

  fetch("../server/cart_controller.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `plant_id=${plantId}&action=add`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        showGlobalToast("Added to cart! 🌿", "success");
      } else {
        showGlobalToast("Failed to add item.", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
});
