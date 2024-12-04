<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$error_message = "";

$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
      session_start();
      $_SESSION["email"] = $email;
      header("Location: index.html");
      exit();
    } 

    else {
      $error_message = "Incorrect password";
    }
    $stmt->close();
  }
  else {
    $error_message = "There is no account with this email, please create an account.";
  }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
    <h1 class="login-title">Log In</h1>
    <form class="login-form" method="POST" action="login.php">
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

    <?php if (!empty($error_message)) : ?>
      <div class="error-message">
        <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>