'use strict';

// Preloader
const preloader = document.querySelector("[data-preaload]");

window.addEventListener("load", function () {
  preloader.classList.add("loaded");
  document.body.classList.add("loaded");
  // Select all menu items
  const menuItems = document.querySelectorAll('.menu-card');
  let basket = JSON.parse(localStorage.getItem('basket')) || []; // Load existing basket or initialize empty

  // Debug: Log initial basket
  console.log('Initial basket:', basket);

  // Add event listeners to menu items
  menuItems.forEach(item => {
    item.addEventListener('click', () => {
      const itemName = item.getAttribute('data-name'); // Get the item name
      if (itemName) {
        basket.push(itemName); // Add item to the basket array
        localStorage.setItem('basket', JSON.stringify(basket)); // Save basket to localStorage

        // Notify the user
        alert(`${itemName} has been added to your basket!`);
        console.log('Updated basket:', basket);
      } else {
        console.error('Menu item missing data-name attribute:', item);
      }
    });
  });
});
  // Add the basket functionality here
  const menuItems = document.querySelectorAll('.menu-card');
  const basket = [];

  menuItems.forEach(item => {
    item.addEventListener('click', () => {
      const itemName = item.getAttribute('data-name');
      basket.push(itemName);
      alert(`${itemName} added to your basket!`);
    });
});

// Event listener helper
const addEventOnElements = function (elements, eventType, callback) {
  for (let i = 0, len = elements.length; i < len; i++) {
    elements[i].addEventListener(eventType, callback);
  }
}

// SEARCH FUNCTIONALITY
const searchInput = document.querySelector('.search-input');
const menuSection = document.getElementById('menu');
const menuCards = document.querySelectorAll('.menu-card');

function scrollToMenu() {
  if (menuSection) {
    const headerHeight = 100; // Adjust based on your header height
    const menuPosition = menuSection.getBoundingClientRect().top + window.pageYOffset - headerHeight;
    window.scrollTo({
      top: menuPosition,
      behavior: 'smooth'
    });
  }
}

function handleSearch() {
  const searchTerm = searchInput.value.toLowerCase().trim();
  let hasResults = false;

  // If search is empty, show all items
  if (!searchTerm) {
    menuCards.forEach(card => card.style.display = 'flex');
    return;
  }

  menuCards.forEach(card => {
    const title = card.querySelector('.card-title').textContent.toLowerCase();
    const description = card.querySelector('.card-text')?.textContent.toLowerCase() || '';
    
    if (title.includes(searchTerm) || description.includes(searchTerm)) {
      card.style.display = 'flex';
      hasResults = true;
    } else {
      card.style.display = 'none';
    }
  });

  if (hasResults) {
    scrollToMenu();
  }
}

// Set up search event listeners
if (searchInput) {
  // Prevent default form submission
  const searchForm = searchInput.closest('form');
  if (searchForm) {
    searchForm.addEventListener('submit', (e) => e.preventDefault());
  }

  // Real-time search
  searchInput.addEventListener('input', handleSearch);
  
  // Handle Enter key
  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      handleSearch();
    }
  });
}

// NAVBAR
const navbar = document.querySelector("[data-navbar]");
const navTogglers = document.querySelectorAll("[data-nav-toggler]");
const overlay = document.querySelector("[data-overlay]");

const toggleNavbar = function () {
  navbar.classList.toggle("active");
  overlay.classList.toggle("active");
  document.body.classList.toggle("nav-active");
}

addEventOnElements(navTogglers, "click", toggleNavbar);

// HEADER & BACK TOP BUTTON
const header = document.querySelector("[data-header]");
const backTopBtn = document.querySelector("[data-back-top-btn]");
let lastScrollPos = 0;

const hideHeader = function () {
  // Don't hide header if search is active
  if (searchInput && searchInput.value.trim() !== '') {
    header.classList.remove('hide');
    return;
  }
  
  const isScrollBottom = lastScrollPos < window.scrollY;
  header.classList.toggle("hide", isScrollBottom);
  lastScrollPos = window.scrollY;
}


window.addEventListener("scroll", function () {
  if (window.scrollY >= 50) {
    header.classList.add("active");
    backTopBtn.classList.add("active");
    hideHeader();
  } else {
    header.classList.remove("active");
    backTopBtn.classList.remove("active");
  }
});

/**
 * HERO SLIDER
 */

const heroSlider = document.querySelector("[data-hero-slider]");
const heroSliderItems = document.querySelectorAll("[data-hero-slider-item]");
const heroSliderPrevBtn = document.querySelector("[data-prev-btn]");
const heroSliderNextBtn = document.querySelector("[data-next-btn]");

let currentSlidePos = 0;
let lastActiveSliderItem = heroSliderItems[0];

const updateSliderPos = function () {
  lastActiveSliderItem.classList.remove("active");
  heroSliderItems[currentSlidePos].classList.add("active");
  lastActiveSliderItem = heroSliderItems[currentSlidePos];
}

const slideNext = function () {
  if (currentSlidePos >= heroSliderItems.length - 1) {
    currentSlidePos = 0;
  } else {
    currentSlidePos++;
  }

  updateSliderPos();
}

heroSliderNextBtn.addEventListener("click", slideNext);

const slidePrev = function () {
  if (currentSlidePos <= 0) {
    currentSlidePos = heroSliderItems.length - 1;
  } else {
    currentSlidePos--;
  }

  updateSliderPos();
}

heroSliderPrevBtn.addEventListener("click", slidePrev);

/**
 * auto slide
 */

let autoSlideInterval;

const autoSlide = function () {
  autoSlideInterval = setInterval(function () {
    slideNext();
  }, 7000);
}

addEventOnElements([heroSliderNextBtn, heroSliderPrevBtn], "mouseover", function () {
  clearInterval(autoSlideInterval);
});

addEventOnElements([heroSliderNextBtn, heroSliderPrevBtn], "mouseout", autoSlide);

window.addEventListener("load", autoSlide);



/**
 * PARALLAX EFFECT
 */

const parallaxItems = document.querySelectorAll("[data-parallax-item]");

let x, y;

window.addEventListener("mousemove", function (event) {

  x = (event.clientX / window.innerWidth * 10) - 5;
  y = (event.clientY / window.innerHeight * 10) - 5;

  // reverse the number eg. 20 -> -20, -5 -> 5
  x = x - (x * 2);
  y = y - (y * 2);

  for (let i = 0, len = parallaxItems.length; i < len; i++) {
    x = x * Number(parallaxItems[i].dataset.parallaxSpeed);
    y = y * Number(parallaxItems[i].dataset.parallaxSpeed);
    parallaxItems[i].style.transform = `translate3d(${x}px, ${y}px, 0px)`;
  }

 // Select the search input and the menu items
const searchInput = document.getElementById('search'); // Assuming your search bar has this ID
const menuItems = document.querySelectorAll('.menu-card'); // Select all menu cards

// Add an event listener to the search input
searchInput.addEventListener('input', () => {
  const query = searchInput.value.toLowerCase().trim(); // Get the input, convert to lowercase, and trim whitespace

  menuItems.forEach(item => {
    // Get the text content of the menu item's title
    const itemName = item.querySelector('.card-title').textContent.toLowerCase();

    // Show or hide the menu item based on the query
    if (itemName.includes(query)) {
      item.style.display = 'block'; // Show the item if it matches
    } else {
      item.style.display = 'none'; // Hide the item if it doesn't match
    }
  });
});
});

