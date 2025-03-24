<?php
function renderFilters() {
    echo '
    <div class="filters-container">
        <div class="filter-group">
            <label for="priceRange">Price Range:</label>
            <select id="priceRange" class="filter-select">
                <option value="all">All Prices</option>
                <option value="0-5">£0 - £5</option>
                <option value="5-10">£5 - £10</option>
                <option value="10+">£10+</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="stockStatus">Availability:</label>
            <select id="stockStatus" class="filter-select">
                <option value="all">All Items</option>
                <option value="in-stock">In Stock</option>
                <option value="out-of-stock">Out of Stock</option>
                <option value="low-stock">Low Stock</option>
            </select>
        </div>
    </div>';
}
?>