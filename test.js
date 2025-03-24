describe('Filters Integration & Unit Tests', () => {
    // Set up DOM elements before each test
    beforeEach(() => {
      document.body.innerHTML = `
        <select id="priceRange">
          <option value="all">All</option>
          <option value="0-5">0-5</option>
          <option value="5-10">5-10</option>
          <option value="10+">10+</option>
        </select>
        <select id="stockStatus">
          <option value="all">All</option>
          <option value="in-stock">in-stock</option>
          <option value="low-stock">low-stock</option>
          <option value="out-of-stock">out-of-stock</option>
        </select>
        <input id="searchInput" value="" />
        
        <div class="main-item" id="item1">
          <span class="price">£4.99</span>
          <span class="stock-status in-stock">In stock</span>
        </div>
        <div class="main-item" id="item2">
          <span class="price">£12.00</span>
          <span class="stock-status low-stock">Low stock</span>
        </div>
      `;
      // Manually dispatch DOMContentLoaded to run filters.js event listener.
      document.dispatchEvent(new Event('DOMContentLoaded'));
    });
  
    // Helper to simulate change events on a given element.
    function triggerChange(el) {
      el.dispatchEvent(new Event('change'));
    }
  
    test('should show items that match price filter "0-5" and stock "all"', () => {
      // Set up: price 0-5 should only show item1 (4.99)
      const priceRange = document.getElementById('priceRange');
      const stockStatus = document.getElementById('stockStatus');
  
      // Set selected values:
      priceRange.value = '0-5';
      stockStatus.value = 'all';
      
      // Fire change events to trigger filterItems
      triggerChange(priceRange);
      triggerChange(stockStatus);
      
      const item1 = document.getElementById('item1');
      const item2 = document.getElementById('item2');
  
      // Expect item1 to be visible and item2 hidden.
      expect(item1.style.display).toBe('block');
      expect(item1.style.visibility).toBe('visible');
      expect(item1.style.opacity).toBe('1');
  
      expect(item2.style.display).toBe('none');
      expect(item2.style.visibility).toBe('hidden');
      expect(item2.style.opacity).toBe('0');
    });
  
    test('should show items that match stock filter "low-stock" regardless of price', () => {
      // Set up: stock filter low-stock should show item2, regardless of price
      const priceRange = document.getElementById('priceRange');
      const stockStatus = document.getElementById('stockStatus');
  
      priceRange.value = 'all'; // no price filtering
      stockStatus.value = 'low-stock';
      
      triggerChange(priceRange);
      triggerChange(stockStatus);
  
      const item1 = document.getElementById('item1');
      const item2 = document.getElementById('item2');
  
      // Expect item2 to be visible and item1 hidden.
      expect(item1.style.display).toBe('none');
      expect(item1.style.visibility).toBe('hidden');
  
      expect(item2.style.display).toBe('block');
      expect(item2.style.visibility).toBe('visible');
    });
  
    test('should update filtering on search input event', () => {
      // For example, if searchInput text does not match any item,
      // all items should be hidden.
      // (Assuming the filterItems function uses the search value by matching
      // text content; modify as needed for your implementation.)
      const searchInput = document.getElementById('searchInput');
      const priceRange = document.getElementById('priceRange');
      const stockStatus = document.getElementById('stockStatus');
      
      // Set filters to "all" so only search determines visibility.
      priceRange.value = 'all';
      stockStatus.value = 'all';
      
      // Type text that doesn't match any item.
      searchInput.value = 'Nonexistent';
      searchInput.dispatchEvent(new Event('input'));
  
      const item1 = document.getElementById('item1');
      const item2 = document.getElementById('item2');
  
      // In our basic test example, we expect filterItems to be re-run,
      // so items should// filepath: tests/filters.test.js
  /**
   * To run these tests, ensure you have Jest installed.
   * You may add "test": "jest" to your package.json scripts.
   */
  
  describe('Filters Integration & Unit Tests', () => {
    // Set up DOM elements before each test
    beforeEach(() => {
      document.body.innerHTML = `
        <select id="priceRange">
          <option value="all">All</option>
          <option value="0-5">0-5</option>
          <option value="5-10">5-10</option>
          <option value="10+">10+</option>
        </select>
        <select id="stockStatus">
          <option value="all">All</option>
          <option value="in-stock">in-stock</option>
          <option value="low-stock">low-stock</option>
          <option value="out-of-stock">out-of-stock</option>
        </select>
        <input id="searchInput" value="" />
        
        <div class="main-item" id="item1">
          <span class="price">£4.99</span>
          <span class="stock-status in-stock">In stock</span>
        </div>
        <div class="main-item" id="item2">
          <span class="price">£12.00</span>
          <span class="stock-status low-stock">Low stock</span>
        </div>
      `;
      // Manually dispatch DOMContentLoaded to run filters.js event listener.
      document.dispatchEvent(new Event('DOMContentLoaded'));
    });
  
    // Helper to simulate change events on a given element.
    function triggerChange(el) {
      el.dispatchEvent(new Event('change'));
    }
  
    test('should show items that match price filter "0-5" and stock "all"', () => {
      // Set up: price 0-5 should only show item1 (4.99)
      const priceRange = document.getElementById('priceRange');
      const stockStatus = document.getElementById('stockStatus');
  
      // Set selected values:
      priceRange.value = '0-5';
      stockStatus.value = 'all';
      
      // Fire change events to trigger filterItems
      triggerChange(priceRange);
      triggerChange(stockStatus);
      
      const item1 = document.getElementById('item1');
      const item2 = document.getElementById('item2');
  
      // Expect item1 to be visible and item2 hidden.
      expect(item1.style.display).toBe('block');
      expect(item1.style.visibility).toBe('visible');
      expect(item1.style.opacity).toBe('1');
  
      expect(item2.style.display).toBe('none');
      expect(item2.style.visibility).toBe('hidden');
      expect(item2.style.opacity).toBe('0');
    });
  
    test('should show items that match stock filter "low-stock" regardless of price', () => {
      // Set up: stock filter low-stock should show item2, regardless of price
      const priceRange = document.getElementById('priceRange');
      const stockStatus = document.getElementById('stockStatus');
  
      priceRange.value = 'all'; // no price filtering
      stockStatus.value = 'low-stock';
      
      triggerChange(priceRange);
      triggerChange(stockStatus);
  
      const item1 = document.getElementById('item1');
      const item2 = document.getElementById('item2');
  
      // Expect item2 to be visible and item1 hidden.
    expect(item1.style.display).toBe('none');
    expect(item1.style.visibility).toBe('hidden');
  
    expect(item2.style.display).toBe('block');
    expect(item2.style.visibility).toBe('visible');
    });
}