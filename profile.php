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

    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
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
} else {
    echo "Please log in to access this page.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Peri Palace</title>

  <!-- Font Links -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

  <!-- Link to existing CSS -->
  <link rel="stylesheet" href="./assets/css/style.css">

  <style>
    /* Login Specific Styles */
    .login-section {
      padding: 60px 0;
      text-align: center;
    }

    .login-container {
      width: 100%;
      max-width: 600px; 
      margin: 0 auto;
      background-color: var(--white-alpha-10); 
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px hsla(0, 0%, 0%, 0.1);
    }

    .login-title {
      margin-bottom: 30px;
      color: var(--smoky-black);
    }

    .login-form .input-group {
      margin-bottom: 20px;
      text-align: left;
    }

    .login-form label {
      display: block;
      font-family: var(--ff-dm-sans);
      font-weight: var(--fw-700);
      color: var(--white);
      margin-bottom: 10px;
    }

    .login-form input {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid var(--white-alpha-10);
      border-radius: 8px;
      font-family: var(--ff-dm-sans);
      font-size: var(--fontSize-7);
      transition: border-color 0.3s ease;
      color: var(--white);
    }

    .login-form input:focus {
      outline: none;
      border-color: var(--white);
    }

    .login-form .login-button {
      width: 100%;
      margin-top: 20px;
      background-color: var(--white-alpha-10);
      color: var(--white);
      border: none;
      padding: 14px;
      font-family: var(--ff-dm-sans);
      font-weight: var(--fw-700);
      text-transform: uppercase;
      letter-spacing: 1px;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }

    .login-form .login-button:hover {
      background-color: var(--davys-grey);
    }

    .signup-section {
      margin-top: 20px;
      font-family: var(--ff-dm-sans);
    }

    .signup-section a {
      color: var(--gold-fusion);
      text-decoration: none;
      font-weight: var(--fw-700);
      transition: color 0.3s ease;
    }

    .signup-section a:hover {
      color: var(--smoky-black);
      text-decoration: underline;
    }

    .error-message {
        color: red;
        margin-top: 10px;
    }
  </style>
</head>
<body id="top" class="loaded">

  <header class="header" data-header>
    <div class="container">
      <a href="index.html" class="logo">
        <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
      </a>

      <nav class="navbar" data-navbar>
        <button class="close-btn" aria-label="close menu" data-nav-toggler>
          <ion-icon name="close-outline" aria-hidden="true"></ion-icon>
        </button>

        <a href="index.html" class="logo">
          <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
        </a>

        <ul class="navbar-list">
          <li class="navbar-item">
            <a href="index.html" class="navbar-link hover-underline">
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
<body>
    <div class="container">
        <h1>Edit Profile</h1>

        <form action="profile.php" method="POST">
            
            <fieldset>
                <legend>Address Information</legend>
                <label for="address">Address:</label>
                <input type="text" style="color:white" name="address" value="<?php echo $addressLine1; ?>"><br>
                <label for="city">City:</label>
                <input type="text" style="color:white" name="city" value="<?php echo $city; ?>" ><br>
                <label for="postalCode">Postal Code:</label>
                <input type="text" style="color:white" name="postalCode" value="<?php echo $postalCode; ?>" ><br>
                <button type="submit"style="color:white"  name="update_address">Update Address</button>
            </fieldset>

           
            <fieldset>
                <legend>User Information</legend>
                <label for="email">Email:</label>
                <input style="color:white" type="email" name="email" value="<?php echo $email; ?>" ><br>
                <label for="phone">Phone Number:</label>
                <input style="color:white" type="text" name="phone" value="<?php echo $phone_number; ?>" ><br>
                <label for="password">Password:</label>
                <input style="color:white" type="password" name="password"><br>
                <button style="color:white" type="submit" name="update_user">Update User Info</button>
            </fieldset>

           
            <fieldset>
                <button type="submit" style="color:white" name="logout">Log Out</button>
            </fieldset>
        </form>
    </div>
</body>

<footer class="footer section has-bg-image text-center"
    style="background-image: url('./assets/images/newBackSpice.jpg')">
    <div class="container">
  <div class="footer-top grid-list">
 <div class="footer-brand has-before has-after">
    <a href="#" class="logo">
            <img src="./assets/images/logoWhite.png" width="160" height="50" loading="lazy" alt="Peri Palace home">
          </a>
          <address class="body-4">
            Corporate Sreet, Stratford Rd, Liverpool 8976, UK
          </address>
          <a href="mailto:Peri-booking@palace.com" class="body-4 contact-link">Peri-booking@palace.com</a>
          <a href="tel:+29056745321" class="body-4 contact-link">Booking Request : +2 905 674 5321</a>
          <p class="body-4">
            Open : 11 am - 11 pm
          </p>
          <div class="wrapper">
            <div class="separator"></div>
            <div class="separator"></div>
            <div class="separator"></div>
          </div>
        </div>
        <ul class="footer-list">
          <li>
            <a href="#contact" class="label-2 footer-link hover-underline">Contact</a>
          </li>
       
      <div class="footer-bottom">
        <p class="copyright">
          &copy; 2024 Peri Place. All Rights Reserved |
        </p>
      </div>
    </div>
  </footer>
</html>
