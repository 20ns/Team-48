
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap" rel="stylesheet">
  <title>Peri Palace - Table Reservation</title>
 <style>
    .reservation-section {
      padding: 60px 0;
      background-color: var(--eerie-black-1);
      color: var(--white);
    }

    .reservation-container {
      max-width: 800px;
      margin: 0 auto;
      padding: 40px;
      background-color: var(--smoky-black-2);
      border-radius: var(--radius-24);
      box-shadow: 0 8px 24px var(--black-alpha-20);
      border: 1px solid var(--gold-crayola-alpha-20);
    }

    .reservation-form {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .form-group {
      margin-bottom: 1rem;
      position: relative;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    label {
      display: block;
      margin-bottom: 0.75rem;
      font-family: var(--ff-dm-sans);
      font-weight: var(--fw-700);
      color: var(--gold-crayola);
      font-size: 1.5rem;
      letter-spacing: 0.5px;
    }

    input, select, textarea {
      width: 100%;
      padding: 14px 18px;
      border: 2px solid var(--white-alpha-15);
      border-radius: 12px;
      background-color: var(--eerie-black-2);
      color: var(--white);
      font-family: var(--ff-dm-sans);
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    input:hover, select:hover, textarea:hover {
      border-color: var(--gold-crayola-alpha-40);
    }

    input:focus, select:focus, textarea:focus {
      outline: none;
      border-color: var(--gold-crayola);
      box-shadow: 0 0 8px var(--gold-crayola-alpha-20);
    }

    .reservation-btn {
      grid-column: 1 / -1;
      background: linear-gradient(135deg, var(--gold-crayola) 0%, var(--gold-crayola-dark) 100%);
      color: var(--smoky-black-1);
      padding: 18px 40px;
      border: none;
      border-radius: 12px;
      font-family: var(--ff-forum);
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 600;
      margin-top: 1rem;
    }

    .reservation-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px var(--gold-crayola-alpha-30);
    }

    .section-title {
      font-size: 2.5rem;
      margin-bottom: 2rem;
      position: relative;
      padding-bottom: 1rem;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 2px;
      background: var(--gold-crayola);
    }

    @media (max-width: 768px) {
      .reservation-container {
        padding: 30px;
        margin: 0 20px;
      }

      .reservation-form {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .section-title {
        font-size: 2rem;
      }
    }

    @media (max-width: 480px) {
      .reservation-container {
        padding: 25px;
      }
      
      input, select, textarea {
        padding: 12px 15px;
      }
    }
</style>
</head>

<body id="top" class="reservation">

  <!-- HEADER SECTION -->
  <header class="header" data-header>
    <div class="container">
      <a href="./index.html" class="logo">
        <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
      </a>
      <nav class="navbar" data-navbar>
        <ul class="navbar-list">
          <li class="navbar-item"><a href="./index.html#home" class="navbar-link hover-underline">Home</a></li>
          <li class="navbar-item"><a href="./index.html#menu" class="navbar-link hover-underline">Menus</a></li>
          <li class="navbar-item"><a href="./index.html#about" class="navbar-link hover-underline">About Us</a></li>
          <li class="navbar-item"><a href="./index.html#contact" class="navbar-link hover-underline">Contact</a></li>
          <li class="navbar-item"><a href="basket.php" class="navbar-link hover-underline">Basket</a></li>
        </ul>
      </nav>
      <button class="nav-open-btn" aria-label="open menu" data-nav-toggler>
        <span class="line line-1"></span>
        <span class="line line-2"></span>
        <span class="line line-3"></span>
      </button>
    </div>
  </header>

  <!-- RESERVATION SECTION -->
  <main>
    <section class="section reservation-section">
      <div class="container">
        <div class="reservation-container">
          <h2 class="headline-1 section-title text-center">Table Reservation</h2>
          <form class="reservation-form" action="reservation.php" method="POST">
            <div class="form-group">
              <label for="name">Full Name</label>
              <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
              <label for="date">Reservation Date</label>
              <input type="date" id="date" name="date" required>
            </div>

            <div class="form-group">
              <label for="time">Reservation Time</label>
              <input type="time" id="time" name="time" required>
            </div>

            <div class="form-group">
              <label for="party-size">Party Size</label>
              <select id="party-size" name="party-size" required>
                <option value="1">1 Person</option>
                <option value="2">2 People</option>
                <option value="3">3 People</option>
                <option value="4">4 People</option>
                <option value="5">5 People</option>
                <option value="6">6 People</option>
                <option value="7">7 People</option>
                <option value="8">8+ People</option>
              </select>
            </div>

            <div class="form-group full-width">
              <label for="special-requests">Special Requests</label>
              <textarea id="special-requests" name="special-requests" rows="3"></textarea>
            </div>

            <button type="submit" class="reservation-btn">Book Table</button>
          </form>
        </div>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="footer section has-bg-image text-center" style="background-image: url('./assets/images/newBackSpice.jpg')">
    <div class="container">
      <div class="footer-top grid-list">
        <div class="footer-brand has-before has-after">
          <a href="./index.html" class="logo">
            <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace home">
          </a>
          <address class="body-4">Corporate Street, Stratford Rd, Liverpool 8976, UK</address>
          <a href="mailto:Peri-booking@palace.com" class="body-4 contact-link">Peri-booking@palace.com</a>
          <a href="tel:+29056745321" class="body-4 contact-link">Booking Request: +2 905 674 5321</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="./assets/js/script.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
