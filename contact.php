<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  
  $name    = $_POST['name']   ?? '';
  $email   = $_POST['email']  ?? '';
  $message = $_POST['message'] ?? '';

  
  $to      = "contact.peripalace@gmail.com";  
  $subject = "New Contact From: $name";

  
  $body  = "Name: $name\n";
  $body .= "Email: $email\n\n";
  $body .= "Message:\n$message\n";

  
  $headers  = "From: $email\r\n";
  $headers .= "Reply-To: $email\r\n";

  
  $mailSent = mail($to, $subject, $body, $headers);

  

  if ($mailSent) {
    // Success page
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Message Sent | Peri Palace</title>
      <link rel="stylesheet" href="assets/css/style.css">

      <!-- Inline style adjustments just for this success page -->
      <style>
        .section.text-center h2.headline-1 {
          line-height: 1.2; /* Prevent “squashed” look */
        }
        .center-button {
          text-align: center;
          margin-top: 2rem;
        }
        .center-button .btn.btn-secondary {
          display: inline-block;
        }
      </style>
    </head>
    <body id="top" class="loaded">
    
      <!-- Header (Copy your real nav if needed) -->
      <header class="header" data-header>
        <div class="container">
          <a href="index.html" class="logo">
            <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
          </a>
          <nav class="navbar" data-navbar>
            <ul class="navbar-list">
              <li class="navbar-item"><a href="index.html" class="navbar-link hover-underline">Home</a></li>
              <li class="navbar-item"><a href="index.html#menu" class="navbar-link hover-underline">Menus</a></li>
              <li class="navbar-item"><a href="index.html#about" class="navbar-link hover-underline">About Us</a></li>
            </ul>
          </nav>
        </div>
      </header>

      <main>
        <section class="section text-center" style="padding: 80px;">
          <h2 class="headline-1">Thank you, ' . htmlspecialchars($name) . '!</h2>
          <p>Your message has been sent. We\'ll be in touch soon.</p>
          
          <div class="center-button">
            <a href="index.html" class="btn btn-secondary">
              <span class="text text-1">Continue Browsing</span>
              <span class="text text-2" aria-hidden="true">Continue Browsing</span>
            </a>
          </div>
        </section>
      </main>
    </body>
    </html>
    ';
  } else {
    // Fail page
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Message Failed | Peri Palace</title>
      <link rel="stylesheet" href="assets/css/style.css">

      <!-- Inline style for the fail page -->
      <style>
        .section.text-center h2.headline-1 {
          line-height: 1.2;
        }
        .center-button {
          text-align: center;
          margin-top: 2rem;
        }
        .center-button .btn.btn-secondary {
          display: inline-block;
        }
      </style>
    </head>
    <body id="top" class="loaded">
    
      <!-- Header (Copy your real nav if needed) -->
      <header class="header" data-header>
        <div class="container">
          <a href="index.html" class="logo">
            <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
          </a>
          <nav class="navbar" data-navbar>
            <ul class="navbar-list">
              <li class="navbar-item"><a href="index.html" class="navbar-link hover-underline">Home</a></li>
              <li class="navbar-item"><a href="index.html#menu" class="navbar-link hover-underline">Menus</a></li>
              <li class="navbar-item"><a href="index.html#about" class="navbar-link hover-underline">About Us</a></li>
            </ul>
          </nav>
        </div>
      </header>

      <main>
        <section class="section text-center" style="padding: 80px;">
          <h2 class="headline-1">Oops!</h2>
          <p>Something went wrong. We couldn\'t send your message at this time.</p>

          <div class="center-button">
            <a href="index.html" class="btn btn-secondary">
              <span class="text text-1">Continue Browsing</span>
              <span class="text text-2" aria-hidden="true">Continue Browsing</span>
            </a>
          </div>
        </section>
      </main>
    </body>
    </html>
    ';
  }

} else {
  // If someone visits contact.php directly (no form submission)
  echo '
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>No Form Data | Peri Palace</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      .section.text-center h2.headline-1 {
        line-height: 1.2;
      }
      .center-button {
        text-align: center;
        margin-top: 2rem;
      }
      .center-button .btn.btn-secondary {
        display: inline-block;
      }
    </style>
  </head>
  <body id="top" class="loaded">
    
    <!-- Minimal navigation (optional) -->
    <header class="header" data-header>
      <div class="container">
        <a href="index.html" class="logo">
          <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
        </a>
      </div>
    </header>

    <main>
      <section class="section text-center" style="padding: 80px;">
        <h2 class="headline-1">No Form Data!</h2>
        <p>Please fill out the <a href="index.html#contact">contact form</a> first.</p>
        
        <div class="center-button">
          <a href="index.html" class="btn btn-secondary">
            <span class="text text-1">Continue Browsing</span>
            <span class="text text-2" aria-hidden="true">Continue Browsing</span>
          </a>
        </div>
      </section>
    </main>
  </body>
  </html>
  ';
}
?>
