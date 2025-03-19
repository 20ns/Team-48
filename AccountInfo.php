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

  <!-- Link to existing CSS -->
  <link rel="stylesheet" href="./assets/css/style.css">

  <style>
    /* Account Info Specific Styles */
    .account-section {
      padding: 60px 0;
      text-align: center;
    }

    .account-container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      background-color: var(--white-alpha-10);
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px hsla(0, 0%, 0%, 0.1);
    }

    .account-title {
      margin-bottom: 30px;
      color: var(--smoky-black);
    }

    .account-details {
      text-align: left;
      font-family: var(--ff-dm-sans);
      font-size: var(--fontSize-7);
      color: var(--white);
      margin-bottom: 20px;
    }

    .account-details span {
      font-weight: var(--fw-700);
    }

    .orders-section {
      margin-top: 40px;
      text-align: left;
    }

    .orders-title {
      margin-bottom: 20px;
      font-family: var(--ff-dm-sans);
      font-size: var(--fontSize-7);
      font-weight: var(--fw-700);
      color: var(--gold-fusion);
    }

    .order-item {
      margin-bottom: 10px;
      font-family: var(--ff-dm-sans);
      color: var(--white);
    }

    .login-message {
      font-size: 18px;
      color: red;
    }

    .edit-btn, .logout-btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: var(--gold-fusion);
      color: white;
      font-size: 16px;
      text-decoration: none;
      border-radius: 5px;
      margin-top: 20px;
      border: 2px solid var(--gold-fusion);
      transition: background-color 0.3s ease;
    }
    
    .edit-btn:hover, .logout-btn:hover {
      background-color: darkorange;
    }

    .edit-btn:focus, .logout-btn:focus {
      outline: none;
      border-color: darkorange;
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
            <a href="index.html#menu" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">Menus</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="index.html#about" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">About Us</span>
            </a>
          </li>
        </ul>
      </nav>

      <a href="#" class="btn btn-secondary">
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
    <article>
      <section class="section account-section" aria-label="account-info">
        <div class="container">
          <div class="account-container">
            <h1 class="headline-1 account-title">Account Information</h1>

            <?php if ($name === null || $email === null || $phone_number === null): ?>
                <div class="login-message">Please log in to view your account details.</div>
            <?php else: ?>
                <div class="account-details">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($phone_number); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($addressLine1); ?></p>
                    <p><strong>City:</strong> <?php echo htmlspecialchars($city); ?></p>
                    <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($postalCode); ?></p>
                </div>
                <div class="orders-section">
                    <h2 class="orders-title">Previous Orders</h2>
                    <!-- Here you can add real order data from the database -->
                    <div class="order-item">Order #1: Grilled Chicken - £8.99 </div>
                    <div class="order-item">Order #2: Garlic Bread - £3.99 </div>
                    <div class="order-item">Order #3: Brownie-Cream Explosion - £4.99</div>
                </div>

                <!-- Edit Button -->
                <a href="profile.php" class="edit-btn">Edit Information</a>

                <!-- Logout Button -->
                <form method="post" action="">
                  <button type="submit" name="logout" class="logout-btn">Logout</button>
                </form>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </article>
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

  <!-- Back to top button -->
  <a href="#top" class="back-top-btn active" aria-label="back to top" data-back-top-btn>
    <ion-icon name="chevron-up" aria-hidden="true"></ion-icon>
  </a>

  <!-- Scripts -->
  <script src="script.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>