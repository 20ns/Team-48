# Peri Palace Restaurant Website

A full-featured restaurant website for Peri Palace, offering online menu browsing, ordering, and account management capabilities.

## Features

### Navigation & Menu
- Interactive menu sections (Starters, Mains, Desserts, Drinks, Sides)
- Search functionality implemented in [`search-bar.js`](assets/js/search-bar.js) for easy menu item discovery
- Responsive navigation with mobile support

### Shopping Experience
- Shopping basket functionality
- Checkout process with billing & shipping information
- Order confirmation system with unique reference numbers ([`paymentconfirmed.html`](paymentconfirmed.html))
- Secure payment processing integration

### User Management
- User account creation and login system
- Account information display ([`AccountInfo.html`](AccountInfo.html))
- Order history tracking
- Personal information management

### Design & UI
- Responsive design using modern CSS
- Custom styling defined in [`style.css`](assets/css/style.css)
- Interactive UI elements like back-to-top button
- Professional restaurant imagery and branding

### Technical Implementation
- PHP backend for user authentication ([`logIn.php`](logIn.php), [`signUp.php`](signUp.php))
- Client-side form validation
- Dynamic content loading
- Cross-browser compatibility with IonIcons integration
### 🌟 **Navigation & Menu**
- Interactive menu sections for:
  - [Starters](starters.php)
  - [Mains](mains.php)
  - [Desserts](desserts.php)
  - [Drinks](drinks.php)
  - [Sides](sides.php)
- Search functionality for menu items.
- Responsive navigation with mobile support.

### 🛒 **Shopping & Checkout**
- Add items to a shopping basket ([`basket.php`](basket.php)).
- Checkout process with order summary and promo code support ([`checkout.php`](checkout.php)).
- Order confirmation system with unique reference numbers ([`paymentconfirmed.html`](paymentconfirmed.html)).

### 👤 **User Management**
- User account creation and login system ([`logIn.php`](logIn.php), [`signUp.php`](signUp.php)).
- Account information display ([`AccountInfo.php`](AccountInfo.php)).
- Order history tracking.
- Profile editing for personal details ([`profile.php`](profile.php)).

### 💬 **Customer Reviews**
- Submit reviews with star ratings ([`index.php`](index.php)).
- Option to post reviews anonymously.
- Display of recent customer reviews.

### 🎨 **Design & UI**
- Modern, responsive design using CSS and custom styles ([`style.css`](assets/css/style.css)).
- Interactive UI elements like sliders, hover effects, and a back-to-top button.
- Professional restaurant imagery and branding.

### 🔧 **Technical Implementation**
- PHP backend for dynamic content and database interactions.
- MySQL database for storing user data, orders, and reviews.
- Secure session management for user authentication.
- Integration with IonIcons for consistent iconography.
