<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'connection.php';

function createShoppingSession($user_id, $pdo) {
    $total = 0.00;
    $created_at = date("Y-m-d H:i:s");
    $modified_at = $created_at;

    try {
        $stmt = $pdo->prepare("INSERT INTO shoppingSession (userID, total, created_at, modified_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $total, $created_at, $modified_at]);
        return $pdo->lastInsertId();

    } catch (PDOException $e) {
        error_log("Error creating shopping session: " . $e->getMessage());
        return false;
    }
}

function updateShoppingSession($session_id, $new_total, $pdo) {
    $modified_at = date("Y-m-d H:i:s");

    try {
        $stmt = $pdo->prepare("UPDATE shoppingSession SET total = ?, modified_at = ? WHERE id = ?");
        $stmt->execute([$new_total, $modified_at, $session_id]);
        return true;

    } catch (PDOException $e) {
        error_log("Error updating shopping session: " . $e->getMessage());
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $session_id = createShoppingSession($user['id'], $pdo);
                if ($session_id !== false) {
                   setcookie('session_id', $session_id, time() + 3600, "/", "", false, true);
                    $_SESSION['userID'] = $user['id'];
                    $_SESSION['loggedin'] = true;

                    header("Location: index.php");
                    exit();
                } else {
                     $error_message = "Failed to create shopping session";
                }

            } else {
                $error_message = "Invalid credentials.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (empty($email) || empty($password)) {
      $error_message = "Please enter both email and password.";
  } else {
      try {
          $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
          $stmt->execute([$email]);
          $user = $stmt->fetch(PDO::FETCH_ASSOC);

          if ($user && password_verify($password, $user['password'])) {
              $stmt = $pdo->prepare("SELECT id FROM shoppingSession WHERE userID = ?");
              $stmt->execute([$user['id']]);
              $existing_session = $stmt->fetch(PDO::FETCH_ASSOC);

              if ($existing_session) {
                  $session_id = $existing_session['id'];
              } else {
                  $session_id = createShoppingSession($user['id'], $pdo);
              }

              if ($session_id !== false) {

                // I've changed the expiration time from an hour to quite a bit more since it was bugging me while testing 
                  setcookie('session_id', $session_id, time() + 10000, "/", "", false, true);
                  $_SESSION['userID'] = $user['id'];
                  $_SESSION['loggedin'] = true;

                  header("Location: index.php");
                  exit();
              } else {
                  $error_message = "Failed to create shopping session";
              }
          } else {
              $error_message = "Invalid credentials.";
          }
      } catch (PDOException $e) {
          $error_message = "Database error: " . $e->getMessage();
      }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Peri Palace</title>

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

    .signup-section {
      margin-top: 20px;
      font-family: var(--ff-dm-sans);
    }

    .signup-section a {
      color: var(--gold-fusion);
      text-decoration: none;
      font-weight: var(--fw-700);
      transition: color: 0.3s ease;
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
    <article>
      <section class="section login-section" aria-label="login">
        <div class="container">
          <div class="login-container">
            <h1 class="headline-1 login-title">Log In</h1>

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form class="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
              <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
              </div>
              <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
              </div>
              <button type="submit" class="login-button">Login</button>
            </form>
            <div class="signup-section">
              <p><a href="signUp.php"> Don't have an account? Create one here</a></p>
              <p><a href="admin/login.php"> Admin log in</a></p>
            </div>
          </div>
        </div>
      </section>
    </article>
  </main>
</html>