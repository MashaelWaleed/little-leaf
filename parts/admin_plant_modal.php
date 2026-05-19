<!-- 🌿 Admin Plant Management Modal -->
<div id="plant-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <h3 id="modal-title">Nurture New Plant</h3>
        
        <!-- The form uses enctype="multipart/form-data" to allow for the File Upload System -->
        <form id="address-form" enctype="multipart/form-data">
            <!-- Hidden inputs to track the specific plant ID and the current action (add/edit) -->
            <input type="hidden" id="address-id" name="plant_id" value="">
            <input type="hidden" id="form-action" name="action" value="add_plant">

            <!-- Plant Name Input -->
            <div class="form-group">
                <label for="field-label">Plant Name</label>
                <input type="text" name="name" id="field-label" required placeholder="e.g., Monstera Deliciosa">
            </div>

            <!-- Category Selection (For Search & Filtering Requirement) -->
            <div class="form-group">
                <label for="field-province">Category</label>
                <select name="category" id="field-province" required>
                        <option value="Indoor">Indoor</option>
                        <option value="Outdoor">Outdoor</option>
                        <option value="Trees">Trees</option>
                        <option value="Tropical">Tropical</option>
                        <option value="Succulent">Succulent</option>
                        <option value="Hanging">Hanging</option>
                        <option value="Flowering">Flowering</option>
                        <option value="Low Light">Low Light</option>
                </select>
            </div>

            <!-- Price Input -->
            <div class="form-group">
                <label for="field-city">Price (SAR)</label>
                <input type="number" name="price" id="field-city" step="0.01" required placeholder="0.00">
            </div>
             <!-- Stock quantity -->
            <div class="form-group">
                <label for="field-stock">Stock Quantity</label>
                <input type="number" name="stock_quantity" id="field-stock" min="0" required placeholder="e.g. 15">
            </div>

            <!-- 🆕 File Upload System: Plant Image -->
            <div class="form-group">
                <label>Plant Image</label>
                <div class="file-input-group">
                    <input type="file" name="plant_image" id="plant_image_input" accept=".jpg,.png,.pdf">
                    <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">
                        Allowed: .jpg, .png, .pdf (Max 2MB)
                    </p>
                </div>
                <!-- Image Preview for UI/UX -->
                <img id="plant-img-preview" src="#" alt="Preview" style="display:none; width: 100px; margin-top: 10px; border-radius: 8px;">
            </div>

            <!-- Modal Footer Actions -->
            <div class="modal-actions" style="margin-top: 20px; display: flex; gap: 10px;">
                <button type="button" class="button secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="button primary" >Save Companion</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Simple Image Preview logic for UI/UX marks
    document.getElementById('plant_image_input').onchange = function (evt) {
        const [file] = this.files;
        if (file) {
            const preview = document.getElementById('plant-img-preview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    };
</script>