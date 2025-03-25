<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);
    $party_size = filter_input(INPUT_POST, 'party_size', FILTER_VALIDATE_INT);
    $special_requests = filter_input(INPUT_POST, 'special_requests', FILTER_SANITIZE_STRING);

    // Validation
    $errors = [];
    if (!$name) $errors[] = 'Name is required';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (!$phone) $errors[] = 'Phone number is required';
    if (!$date) $errors[] = 'Date is required';
    if (!$time) $errors[] = 'Time is required';
    if (!$party_size || $party_size < 1) $errors[] = 'Party size is required';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reservations (name, email, phone, date, time, party_size, special_requests) 
                                 VALUES (:name, :email, :phone, :date, :time, :party_size, :special_requests)");
            
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':date' => $date,
                ':time' => $time,
                ':party_size' => $party_size,
                ':special_requests' => $special_requests
            ]);

            $_SESSION['success'] = 'Reservation successfully submitted! We look forward to seeing you.';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

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
      color: var(--gold-crayola-dark);
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

    .alert {
        padding: 15px;
        margin: 20px auto;
        max-width: 800px;
        border-radius: 8px;
        font-family: var(--ff-dm-sans);
    }
    .alert.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
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
  <header class="header" data-header>
    <div class="container">
      <a href="./index.php" class="logo">
        <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
      </a>
      <nav class="navbar" data-navbar>
        <ul class="navbar-list">
          <li class="navbar-item"><a href="./index.php#home" class="navbar-link hover-underline">Home</a></li>
          <li class="navbar-item"><a href="./index.php#menu" class="navbar-link hover-underline">Menus</a></li>
          <li class="navbar-item"><a href="./index.php#about" class="navbar-link hover-underline">About Us</a></li>
          <li class="navbar-item"><a href="./index.php#contact" class="navbar-link hover-underline">Contact</a></li>
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

  <main>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <section class="section reservation-section">
      <div class="container">
        <div class="reservation-container">
          <h2 class="headline-1 section-title text-center">Table Reservation</h2>
          <form class="reservation-form" method="POST">
            <div class="form-group">
              <label for="name">Full Name</label>
              <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label for="date">Reservation Date</label>
              <input type="date" id="date" name="date" required value="<?= htmlspecialchars($_POST['date'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label for="time">Reservation Time</label>
              <input type="time" id="time" name="time" required value="<?= htmlspecialchars($_POST['time'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label for="party-size">Party Size</label>
              <select id="party-size" name="party_size" required>
                <?php $selected = $_POST['party_size'] ?? ''; ?>
                <option value="1" <?= $selected == 1 ? 'selected' : '' ?>>1 Person</option>
                <option value="2" <?= $selected == 2 ? 'selected' : '' ?>>2 People</option>
                <option value="3" <?= $selected == 3 ? 'selected' : '' ?>>3 People</option>
                <option value="4" <?= $selected == 4 ? 'selected' : '' ?>>4 People</option>
                <option value="5" <?= $selected == 5 ? 'selected' : '' ?>>5 People</option>
                <option value="6" <?= $selected == 6 ? 'selected' : '' ?>>6 People</option>
                <option value="7" <?= $selected == 7 ? 'selected' : '' ?>>7 People</option>
                <option value="8" <?= $selected == 8 ? 'selected' : '' ?>>8+ People</option>
              </select>
            </div>

            <div class="form-group full-width">
              <label for="special-requests">Special Requests</label>
              <textarea id="special-requests" name="special_requests" rows="3"><?= htmlspecialchars($_POST['special_requests'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="reservation-btn">Book Table</button>
          </form>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer section has-bg-image text-center" style="background-image: url('./assets/images/newBackSpice.jpg')">
    <div class="container">
      <div class="footer-top grid-list">
        <div class="footer-brand has-before has-after">
          <a href="./index.php" class="logo">
            <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace home">
          </a>
          <address class="body-4">Corporate Street, Stratford Rd, Liverpool 8976, UK</address>
          <a href="mailto:Peri-booking@palace.com" class="body-4 contact-link">Peri-booking@palace.com</a>
          <a href="tel:+29056745321" class="body-4 contact-link">Booking Request: +2 905 674 5321</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="./assets/js/script.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>