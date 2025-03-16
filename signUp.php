<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'connection.php'; 

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($phone) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid Email";
    } else {
        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $error_message = "Email already in use. Please try a different email.";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert the new user
                $stmt = $pdo->prepare("INSERT INTO users (name, phone_number, email, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $phone, $email, $hashed_password]);

                $success_message = "Signup successful! <a href='logIn.php'>Go to Login</a>";
                header("Location: logIn.php");
                exit;
            }
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
             error_log("Signup error: " . $e->getMessage()); // Log the detailed error

        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

  <link rel="stylesheet" href="./assets/css/style.css">

  <style>
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
     .error-message {
        color: red;
        margin-top: 10px;
    }
  </style>
</head>

<body>
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

  <main>
    <section class="login-section">
      <div class="login-container">
        <h1 class="headline-1 login-title">Sign Up</h1>

        <?php if (!empty($error_message)): ?>
          <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
          <div style="color: green;"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
          <div class="input-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" required>
          </div>

          <div class="input-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
          </div>

          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
          </div>

          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
          </div>

          <button type="submit" class="login-button">Sign Up</button>
        </form>
        <p><a href="logIn.php"> Already have an account? Sign in here</a></p>
      </div>
    </section>
  </main>
    <footer class="footer section has-bg-image text-center">
    <div class="container">
      <div class="footer-bottom">
        <p class="copyright">
          © 2024 Peri Palace. All Rights Reserved
        </p>
      </div>
    </div>
  </footer>

  <a href="#top" class="back-top-btn active" aria-label="back to top" data-back-top-btn>
    <ion-icon name="chevron-up" aria-hidden="true"></ion-icon>
  </a>
</body>
  <script src="script.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</html>