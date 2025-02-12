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

## Project Structure

```text
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── images/
│   └── js/
│       └── search-bar.js
├── html pages (menu sections)
│   ├── starters.html
│   ├── mains.html
│   ├── desserts.html
│   ├── drinks.html
│   └── sides.html
├── user management
│   ├── logIn.html/php
│   └── signUp.html/php
└── order processing
    ├── basket.html/php
    ├── Checkout.html
    └── paymentconfirmed.html