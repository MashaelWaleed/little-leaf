document.querySelectorAll(".add-to-cart-btn").forEach((button) => {
  button.addEventListener("click", function () {
    const plantId = this.getAttribute("data-id");

    // Use backticks (`) for the whole string to easily inject ${variables}
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
});
