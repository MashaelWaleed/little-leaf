/**
 * Admin Management Logic for Little Leaf
 * Handles AJAX interactions for the botanical collection.
 */
function showToast(message, type = "success") {
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

const plantList = document.getElementById("admin-plant-list");
const plantModal = document.getElementById("plant-modal"); // Reusing old modal structure
const plantForm = document.getElementById("address-form");

// --- 1. DELETE PLANT (AJAX) ---
async function deletePlant(plantId) {
  if (!confirm("Are you sure you want to remove this botanical companion? 🥀"))
    return;

  const formData = new FormData();
  formData.append("action", "delete_plant");
  formData.append("plant_id", plantId);

  try {
    const response = await fetch("../server/admin_controller.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();

    if (result.status === "success") {
      // Dynamically remove the row from the table
      const row = document.querySelector(`tr[data-id="${plantId}"]`);
      row.style.opacity = "0";
      setTimeout(() => row.remove(), 300);
      showToast("Plant removed from sanctuary. 🌿", "success");
    }
  } catch (error) {
    showToast("Failed to delete plant. 🥀", "error");
  }
}

// --- 2. OPEN MODAL FOR ADDING ---
function openAddPlantModal() {
  plantForm.reset();
  document.getElementById("address-id").value = "";
  document.getElementById("form-action").value = "add_plant";
  document.getElementById("modal-title").innerText = "Nurture New Plant";
  plantModal.style.display = "flex";
}
function closeModal() {
  document.getElementById("plant-modal").style.display = "none";
}

// --- 3. OPEN MODAL FOR EDITING ---
function editPlant(plant) {
  document.getElementById("address-id").value = plant.id;
  document.getElementById("form-action").value = "edit_plant";
  document.getElementById("modal-title").innerText = "Edit Companion Details";

  // Mapping plant data to form fields
  document.getElementById("field-label").value = plant.name;
  document.getElementById("field-city").value = plant.price;
  document.getElementById("field-province").value = plant.category;
  document.getElementById("field-stock").value = plant.stock_quantity;

  plantModal.style.display = "flex";
}

// --- 4. HANDLE FORM SUBMISSION (Add/Edit via AJAX) ---
plantForm?.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(plantForm);
  const action = formData.get("action");

  try {
    const response = await fetch("../server/admin_controller.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();

    if (result.status === "success") {
      if (action === "add_plant") {
        // Dynamically append the new row
        const newRow = createPlantRowHTML(result.data);
        plantList.insertAdjacentHTML("afterbegin", newRow);
        showToast("New plant added to the collection! 🌿", "success");
        closeModal();
      } else {
        // Update the existing row
        const row = document.querySelector(
          `tr[data-id="${formData.get("plant_id")}"]`,
        );
        updatePlantRowUI(row, result.data);
        showToast("Plant details updated. 🌿", "success");
        closeModal();
      }
    }
  } catch (error) {
    showToast("Error processing request. 🥀", "error");
  }
});

// --- HELPER FUNCTIONS ---
function createPlantRowHTML(plant) {
  return `
        <tr data-id="${plant.id}">
            <td><img src="../images/products/${plant.image}" width="50"></td>
            <td>${plant.name}</td>
            <td>${plant.category}</td>
            <td>${plant.price} SAR</td>
            <td>${plant.stock_quantity}</td>
            <td>
                <button class="text-btn" onclick='editPlant(${JSON.stringify(plant)})'>Edit</button>
                <button class="text-btn danger" onclick="deletePlant(${plant.id})">Delete</button>
            </td>
        </tr>`;
}

function updatePlantRowUI(row, data) {
  const cells = row.querySelectorAll("td");

  // 1. Update the Image (First TD)
  const imgTag = cells[0].querySelector("img");
  if (imgTag && data.image) {
    // Adding ?t= ensures the browser doesn't show an old cached version
    imgTag.src = `../images/products/${data.image}?t=${new Date().getTime()}`;
  }

  // 2. Update Text Fields
  cells[1].innerText = data.name;
  cells[2].innerText = data.category;
  cells[3].innerText = `${data.price} SAR`;
  cells[4].innerText = data.stock_quantity;

  // 3. Update the Edit Button data
  // This is vital so if you click 'Edit' again, it has the NEW info
  const editBtn = row.querySelector("button[onclick^='editPlant']");
  if (editBtn) {
    editBtn.setAttribute("onclick", `editPlant(${JSON.stringify(data)})`);
  }
}
