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

    $query = "SELECT * FROM userinfo WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        $userInfo = $result->fetch_assoc();
        $address = $userInfo['addressLine1'];
        $city = $userInfo['city'];
        $postalCode = $userInfo['postalCode'];
    } else {
        // No info found
        $address = $city = $postalCode = '';
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_address'])) {
        $address = $_POST['address'];
        $city = $_POST['city'];
        $postalCode = $_POST['postalCode'];

        if ($result->num_rows == 0) {
            $insertQuery = "INSERT INTO userinfo (userID, addressLine1, city, postalCode) VALUES (?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param('isss', $userID, $address, $city, $postalCode);
            $insertStmt->execute();
            $insertStmt->close();
            echo "Address information updated successfully!";
        } else {
            $updateQuery = "UPDATE userinfo SET addressLine1 = ?, city = ?, postalCode = ? WHERE userID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('sssi', $address, $city, $postalCode, $userID);
            $updateStmt->execute();
            $updateStmt->close();
            echo "Address information updated successfully!";
        }
    }

    // For handle password change (not working right now for whatever reason will try fix later)
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $passwordQuery = "UPDATE users SET password = ? WHERE id = ?";
            $passwordStmt = $conn->prepare($passwordQuery);
            $passwordStmt->bind_param('si', $hashedPassword, $userID);
            $passwordStmt->execute();
            $passwordStmt->close();
            echo "Password updated successfully!";
        } else {
            echo "Passwords do not match!";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_email'])) {
        $newEmail = $_POST['new_email'];
        $emailQuery = "UPDATE users SET email = ? WHERE id = ?";
        $emailStmt = $conn->prepare($emailQuery);
        $emailStmt->bind_param('si', $newEmail, $userID);
        $emailStmt->execute();
        $emailStmt->close();
        echo "Email updated successfully!";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_phone'])) {
        $newPhone = $_POST['new_phone'];
        $phoneQuery = "UPDATE users SET phone_number = ? WHERE id = ?";
        $phoneStmt = $conn->prepare($phoneQuery);
        $phoneStmt->bind_param('si', $newPhone, $userID);
        $phoneStmt->execute();
        $phoneStmt->close();
        echo "Phone number updated successfully!";
    }

    if (isset($_POST['logout'])) {
        // Used to delete session from database but keeping that now so basket is retained across logins
        

        // Only delete session
        session_destroy();
        setcookie('session_id', '', time() - 3600, '/');
        header("Location: logIn.php"); // Redirect to login page
        exit();
    }

    $conn->close();
} else {
    header("Location: logIn.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - Peri Palace</title>

  <!-- Font Links -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

  <!-- Link to existing CSS -->
  <link rel="stylesheet" href="./assets/css/style.css">

  <style>
    .profile-section {
      padding: 60px 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .profile-container {
      flex: 1;
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      padding: 40px;
      background-color: var(--smoky-black-2);
      border-radius: var(--radius-24);
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .profile-title {
      color: var(--gold-crayola);
      text-align: center;
      margin-bottom: 40px;
      font-size: 2.5rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .profile-fieldset {
      border: 1px solid var(--smoky-black-3);
      border-radius: var(--radius-24);
      padding: 25px;
      margin-bottom: 30px;
    }

    .profile-legend {
      color: var(--gold-crayola);
      font-weight: 700;
      padding: 0 15px;
      font-size: 1.2rem;
    }

    .profile-label {
      display: block;
      margin-bottom: 10px;
      color: var(--quick-silver);
      font-weight: 500;
    }

    .profile-input {
      width: 100%;
      padding: 12px 18px;
      background-color: var(--eerie-black-2);
      border: 1px solid var(--smoky-black-3);
      border-radius: var(--radius-24);
      color: var(--white);
      margin-bottom: 20px;
      transition: all var(--transition-1);
    }

    .profile-input:focus {
      border-color: var(--gold-crayola);
      box-shadow: 0 0 0 2px var(--gold-crayola);
      outline: none;
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
      margin: 10px 0;
    }

    .profile-button:hover {
      background-color: var(--white);
      transform: translateY(-2px);
    }

    .logout-button {
      background-color: #dc3545;
      color: var(--white);
      width: 100%;
      justify-content: center;
    }

    .logout-button:hover {
      background-color: #bb2d3b;
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
    <section class="section profile-section">
      <div class="profile-container">
        <h1 class="profile-title">Edit Profile</h1>

        <form action="profile.php" method="POST">
          <!-- Address Information -->
          <fieldset class="profile-fieldset">
            <legend class="profile-legend">Address Information</legend>
            <label class="profile-label" for="address">Address:</label>
            <input class="profile-input" type="text" name="address" value="<?php echo htmlspecialchars($address); ?>">
            
            <label class="profile-label" for="city">City:</label>
            <input class="profile-input" type="text" name="city" value="<?php echo htmlspecialchars($city); ?>">
            
            <label class="profile-label" for="postalCode">Postal Code:</label>
            <input class="profile-input" type="text" name="postalCode" value="<?php echo htmlspecialchars($postalCode); ?>">
            
            <button type="submit" class="profile-button" name="update_address">
              <span class="material-symbols-outlined">location_on</span>
              Update Address
            </button>
          </fieldset>

          <!-- User Information -->
          <fieldset class="profile-fieldset">
            <legend class="profile-legend">User Information</legend>
            
            <label class="profile-label" for="email">Email:</label>
            <input class="profile-input" type="email" name="new_email" value="<?php echo htmlspecialchars($email); ?>">
            <button type="submit" class="profile-button" name="update_email">
              <span class="material-symbols-outlined">mail</span>
              Update Email
            </button>

            <label class="profile-label" for="phone">Phone Number:</label>
            <input class="profile-input" type="text" name="new_phone" value="<?php echo htmlspecialchars($phone_number); ?>">
            <button type="submit" class="profile-button" name="update_phone">
              <span class="material-symbols-outlined">call</span>
              Update Phone
            </button>

            <label class="profile-label" for="password">New Password:</label>
            <input class="profile-input" type="password" name="new_password">
            
            <label class="profile-label" for="password">Confirm Password:</label>
            <input class="profile-input" type="password" name="confirm_password">
            
            <button type="submit" class="profile-button" name="update_password">
              <span class="material-symbols-outlined">lock_reset</span>
              Update Password
            </button>
          </fieldset>

          <!-- Logout -->
          <fieldset class="profile-fieldset">
            <button type="submit" class="profile-button logout-button" name="logout">
              <span class="material-symbols-outlined">logout</span>
              Log Out
            </button>
          </fieldset>
        </form>
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