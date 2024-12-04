// Ensure the DOM is fully loaded before executing the script
window.addEventListener('DOMContentLoaded', () => {
  fetch('./search-bar.html')
    .then(response => response.text())
    .then(data => {
      document.getElementById('search-bar-container').innerHTML = data;
    })
    .catch(error => console.error('Error loading search bar:', error));

  // Attach search functionality
  const searchInput = document.getElementById('searchInput');
  searchInput.addEventListener('input', searchMenu);
});

// Search functionality
function searchMenu() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const menuItems = document.querySelectorAll('.main-item');

  menuItems.forEach(item => {
    const itemName = item.querySelector('.card-title').textContent.toLowerCase();
    if (itemName.includes(searchTerm)) {
      item.style.display = 'block';
    } else {
      item.style.display = 'none';
    }
  });
}
