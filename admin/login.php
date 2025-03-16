<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// If already logged in, redirect to dashboard.php
if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
    header("Location: dashboard.php");
    exit();
}

require_once '../connection.php'; // Correct path

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); // Trim whitespace
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, password FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Successful login!
                $_SESSION['admin_loggedin'] = true;
                header("Location: dashboard.php"); // Redirect to dashboard.php
                exit();
            } else {
                $error = "Invalid credentials.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Peri Palace</title>

    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        /* Your CSS styles */
          .admin-login {
            max-width: 400px;
            margin: 5rem auto;
            padding: 2rem;
            background: var(--white-alpha-10);
            border-radius: 10px;
        }


          .admin-login input {
            width: 100%;
        	padding: 12px 16px;
      		border: 1px solid var(--white-alpha-10);
      		border-radius: 8px;
      		font-family: var(--ff-dm-sans);
      		font-size: var(--fontSize-7);
      		transition: border-color 0.3s ease;
      		color: var(--white);
            margin-bottom: 1rem;
        }

        .error {
            color: red;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-login">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Admin Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>