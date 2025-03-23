<?php 

session_start();

if (isset($_SESSION['userID'])) {
    
    $servername = "localhost";
    $username = "cs2team48";
    $password = "9ZReO56gOBkKTcr";
    $dbname = "cs2team48_db";
    $conn = new mysqli($servername, $username, $password, $dbname);

   
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $userID = $_SESSION['userID'];

    $stmt = $conn->prepare("SELECT name, email, phone_number FROM users WHERE id = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($name, $email, $phone_number);
        $stmt->fetch();
    } else {
        $name = "Unknown";
        $email = "Unknown";
        $phone_number = "Unknown";
    }

    $stmt = $conn->prepare("SELECT addressLine1, city, postalCode FROM userinfo WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($addressLine1, $city, $postalCode);
        $stmt->fetch();
    } else {
        $addressLine1 = "Unknown";
        $city = "Unknown";
        $postalCode = "Unknown";
    }

    $stmt->close();
} else {
    $name = $email = $phone_number = $addressLine1 = $city = $postalCode = null;
}

if (isset($_POST['logout']) && isset($_SESSION['userID'])) {
    
    $servername = "localhost";
    $username = "cs2team48";
    $password = "9ZReO56gOBkKTcr";
    $dbname = "cs2team48_db";
    $conn = new mysqli($servername, $username, $password, $dbname);

   
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $userID = $_SESSION['userID'];
    
    $deleteSessionQuery = "DELETE FROM shoppingSession WHERE userID = ?";
    $deleteSessionStmt = $conn->prepare($deleteSessionQuery);
    $deleteSessionStmt->bind_param('i', $userID);
    $deleteSessionStmt->execute();
    $deleteSessionStmt->close();

    session_destroy();
    setcookie('session_id', '', time() - 3600, '/');
    header("Location: logIn.php"); 
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Information - Peri Palace</title>

  <!-- Font Links -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

  <!-- Link to existing CSS -->
  <link rel="stylesheet" href="./assets/css/style.css">

  <style>
    .account-section {
      padding: 60px 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .account-container {
      flex: 1;
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      padding: 40px;
      background-color: var(--smoky-black-2);
      border-radius: var(--radius-24);
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .account-title {
      color: var(--gold-crayola);
      text-align: center;
      margin-bottom: 40px;
      font-size: 2.5rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .account-details {
      background-color: var(--eerie-black-2);
      border-radius: var(--radius-24);
      padding: 30px;
      margin-bottom: 30px;
    }

    .detail-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 0;
      border-bottom: 1px solid var(--smoky-black-3);
    }

    .detail-item:last-child {
      border-bottom: none;
    }

    .detail-label {
      color: var(--quick-silver);
      font-weight: 500;
      font-size: 1.1rem;
    }

    .detail-value {
      color: var(--white);
      font-weight: 600;
      max-width: 60%;
      text-align: right;
    }

    .orders-section {
      margin-top: 40px;
      background-color: var(--eerie-black-2);
      border-radius: var(--radius-24);
      padding: 30px;
    }

    .orders-title {
      color: var(--gold-crayola);
      font-size: 1.5rem;
      margin-bottom: 25px;
      text-transform: uppercase;
    }

    .order-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 0;
      border-bottom: 1px solid var(--smoky-black-3);
    }

    .order-item:last-child {
      border-bottom: none;
    }

    .action-buttons {
      margin-top: 30px;
      display: flex;
      gap: 20px;
      justify-content: center;
    }

    .profile-button {
      padding: 12px 24px;
      background-color: var(--gold-crayola);
      color: var(--smoky-black-1);
      border: none;
      border-radius: var(--radius-24);
      cursor: pointer;
      font-weight: 700;
      transition: all var(--transition-1);
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
    }

    .profile-button:hover {
      background-color: var(--white);
      transform: translateY(-2px);
    }

    .logout-button {
      background-color: #dc3545;
      color: var(--white);
    }

    .logout-button:hover {
      background-color: #bb2d3b;
    }

    .login-message {
      text-align: center;
      color: var(--gold-crayola);
      font-size: 1.2rem;
      padding: 30px;
    }
  </style>
</head>

<body id="top" class="loaded">
  <!-- Header -->
  <header class="header" data-header>
    <div class="container">
      <a href="index.php" class="logo">
        <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
      </a>

      <nav class="navbar" data-navbar>
        <button class="close-btn" aria-label="close menu" data-nav-toggler>
          <ion-icon name="close-outline" aria-hidden="true"></ion-icon>
        </button>

        <a href="index.php" class="logo">
          <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
        </a>

        <ul class="navbar-list">
          <li class="navbar-item">
            <a href="index.php" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">Home</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="index.php#menu" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">Menus</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="index.php#about" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">About Us</span>
            </a>
          </li>
        </ul>
      </nav>

      <a href="reservation.php" class="btn btn-secondary">
        <span class="text text-1">Book A Table</span>
        <span class="text text-2" aria-hidden="true">Book A Table</span>
      </a>

      <button class="nav-open-btn" aria-label="open menu" data-nav-toggler>
        <span class="line line-1"></span>
        <span class="line line-2"></span>
        <span class="line line-3"></span>
      </button>
    </div>
  </header>

  <main>
    <section class="section account-section">
      <div class="account-container">
        <h1 class="account-title">Account Overview</h1>

        <?php if ($name === null || $email === null || $phone_number === null): ?>
          <div class="login-message">Please log in to view your account details</div>
        <?php else: ?>
          <div class="account-details">
            <div class="detail-item">
              <span class="detail-label">Full Name:</span>
              <span class="detail-value"><?php echo htmlspecialchars($name); ?></span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Email Address:</span>
              <span class="detail-value"><?php echo htmlspecialchars($email); ?></span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Phone Number:</span>
              <span class="detail-value"><?php echo htmlspecialchars($phone_number); ?></span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Address:</span>
              <span class="detail-value"><?php echo htmlspecialchars($addressLine1); ?></span>
            </div>
            <div class="detail-item">
              <span class="detail-label">City:</span>
              <span class="detail-value"><?php echo htmlspecialchars($city); ?></span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Postal Code:</span>
              <span class="detail-value"><?php echo htmlspecialchars($postalCode); ?></span>
            </div>
          </div>

          <div class="orders-section">
            <h2 class="orders-title">Order History</h2>
            <div class="order-item">
              <span>Order #1: Grilled Chicken</span>
              <span>£8.99</span>
            </div>
            <div class="order-item">
              <span>Order #2: Garlic Bread</span>
              <span>£3.99</span>
            </div>
            <div class="order-item">
              <span>Order #3: Brownie-Cream Explosion</span>
              <span>£4.99</span>
            </div>
          </div>

          <div class="action-buttons">
            <a href="profile.php" class="profile-button">
              <span class="material-symbols-outlined">edit</span>
              Edit Profile
            </a>
            <form method="post" action="">
              <button type="submit" name="logout" class="profile-button logout-button">
                <span class="material-symbols-outlined">logout</span>
                Log Out
              </button>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer section has-bg-image text-center">
    <div class="container">
      <div class="footer-bottom">
        <p class="copyright">
          &copy; 2024 Peri Palace. All Rights Reserved
        </p>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="script.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>