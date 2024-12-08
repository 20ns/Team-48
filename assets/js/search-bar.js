// make sure that DOM is loaded so that the search bar can function
window.addEventListener('DOMContentLoaded', () => {
  fetch('./search-bar.html')
    .then(response => response.text())
    .then(data => {
      document.getElementById('search-bar-container').innerHTML = data;
    })
    .catch(error => console.error('Error loading search bar:', error));

  // Connect to the search bar
  const searchInput = document.getElementById('searchInput');
  searchInput.addEventListener('input', searchMenu);
});

// search bar actual code
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
