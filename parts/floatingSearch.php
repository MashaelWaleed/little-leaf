<div id="searchOverlay" class="search-overlay">
    <div class="search-container">
        <div class="closeBtn-Container">
         <button id="closeBtn" class="closeBtn">X</button>
         </div>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search for a plant..." autocomplete="off" >
            
        </div>
        
        <div class="filter-controls">
            <select id="categoryFilter" class="categoryFilter">
                <option value="">All Categories</option>
                <option value="Indoor">Indoor</option>
                <option value="Outdoor">Outdoor</option>
                <option value="Trees">Trees</option>
                <option value="Tropical">Tropical</option>
                <option value="Succulent">Succulent</option>
                <option value="Hanging">Hanging</option>
                <option value="Flowering">Flowering</option>
                <option value="Low Light">Low Light</option>
            </select>

            <div class="priceFilter">
                <label>Max Price: <span id="priceVal">500</span> SAR</label>
                <input type="range" id="priceFilter" min="0" max="500" value="500">
            </div>
        </div>
    </div>
</div>