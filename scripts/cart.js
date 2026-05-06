document.addEventListener("DOMContentLoaded", () => {
  const qtyButtons = document.querySelectorAll(".qty-btn");

  qtyButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const plantId = this.getAttribute("data-id");
      const action = this.innerText === "+" ? "increase" : "decrease";
      const qtySpan = this.parentElement.querySelector(".qty-number");
      let currentQty = parseInt(qtySpan.innerText);

      // Prevent going below 1 (unless you want to auto-remove)
      if (action === "decrease" && currentQty <= 1) return;

      fetch("../server/cart_controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `plant_id=${plantId}&action=${action}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            // 1. Update the quantity number on the screen immediately
            qtySpan.innerText = data.new_qty;

            // 2. 🆕 DYNAMIC TOTALS: Update the summary prices without reloading
            // API returns the new subtotal and total
            if (data.new_subtotal) {
              document.querySelector(".summary-row span:last-child").innerText =
                `${data.new_subtotal} SAR`;
              document.querySelector(".total-row span:last-child").innerText =
                `${data.new_total} SAR`;
            }

            showGlobalToast("Cart updated! 🌿", "success");
          }
        });
    });
  });
});

document.querySelectorAll(".remove-btn").forEach((button) => {
  button.addEventListener("click", function () {
    const plantId = this.getAttribute("data-id");
    // Find the specific card element to remove later
    const cartItemCard = this.closest(".cart-item-card");

    fetch("../server/cart_controller.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `plant_id=${plantId}&action=remove`,
    })
      .then((response) => response.json()) // Ensure your PHP returns JSON
      .then((data) => {
        if (data.status === "success") {
          // 1. Remove the item from the UI with a smooth transition
          cartItemCard.style.opacity = "0";
          cartItemCard.style.transform = "scale(0.9)";

          setTimeout(() => {
            cartItemCard.remove();

            // 2. Update the totals dynamically if the API provided them
            if (data.new_total !== undefined) {
              document.querySelector(".total-row span:last-child").innerText =
                `${data.new_total} SAR`;
              document.querySelector(".summary-row span:last-child").innerText =
                `${data.new_subtotal} SAR`;
            }

            // 3. Show an empty state if no items are left
            const remainingItems = document.querySelectorAll(".cart-item-card");
            if (remainingItems.length === 0) {
              document.querySelector(".cart-items-list").innerHTML = `
                            <div class="empty-card">
                                <img src="../images/logo.svg" alt="leaf icon">
                                <p>Your cart is empty</p>
                                <a href="products.php">Go shopping!</a>
                            </div>`;
            }

            showGlobalToast("Companion removed from cart. 🌿", "success");
          }, 300);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showGlobalToast("Could not remove item. 🥀", "error");
      });
  });
});
