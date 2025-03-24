document.addEventListener('DOMContentLoaded', function() {
    const priceRange = document.getElementById('priceRange');
    const stockStatus = document.getElementById('stockStatus');
    const items = document.querySelectorAll('.main-item');

    function filterItems() {
        const selectedPrice = priceRange.value;
        const selectedStock = stockStatus.value;

        items.forEach(item => {
            const price = parseFloat(item.querySelector('.price').textContent.replace('£', ''));
            const stockElement = item.querySelector('.stock-status');
            const stockStatusText = stockElement.classList.contains('in-stock') ? 'in-stock' :
                                  stockElement.classList.contains('low-stock') ? 'low-stock' : 'out-of-stock';

            let showByPrice = true;
            let showByStock = true;

            // Price range filtering
            if (selectedPrice !== 'all') {
                if (selectedPrice === '0-5') showByPrice = price <= 5;
                else if (selectedPrice === '5-10') showByPrice = price > 5 && price <= 10;
                else if (selectedPrice === '10+') showByPrice = price > 10;
            }

            // Stock status filtering
            if (selectedStock !== 'all') {
                showByStock = stockStatus.value === stockStatusText;
            }

            // Use visibility and grid properties instead of display
            if (showByPrice && showByStock) {
                item.style.display = 'block';
                item.style.visibility = 'visible';
                item.style.position = 'relative';
                item.style.opacity = '1';
            } else {
                item.style.display = 'none';
                item.style.visibility = 'hidden';
                item.style.position = 'absolute';
                item.style.opacity = '0';
            }
        });
    }

    if (priceRange && stockStatus) {
        priceRange.addEventListener('change', filterItems);
        stockStatus.addEventListener('change', filterItems);

        // Add search integration
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterItems(); // Apply filters after search
            });
        }
    }
});