<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
    header("Location: dashboard.php");
    exit();
}

require_once '../connection.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, password FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_loggedin'] = true;
                header("Location: dashboard.php");
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

    <!-- Font Links -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

    <link rel="stylesheet" href="./assets/css/style.css">

    <style>
    :root {
        --gold-crayola: #e4c590;
        --smoky-black-1: #0A0A0A;
        --smoky-black-2: #1A1A1A;
        --smoky-black-3: #2A2A2A;
        --eerie-black-1: #121212;
        --eerie-black-2: #1E1E1E;
        --quick-silver: #A0A0A0;
        --white: #ffffff;
        --radius-24: 24px;
        --transition-1: 0.25s ease;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        background-color: var(--eerie-black-1);
        color: var(--white);
        min-height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .admin-login {
        width: 100%;
        max-width: 400px;
        padding: 40px;
        background-color: var(--smoky-black-2);
        border-radius: var(--radius-24);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        border: 1px solid var(--smoky-black-3);
    }

    h2 {
        color: var(--gold-crayola);
        text-align: center;
        margin-bottom: 30px;
        font-size: 2rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .error {
        color: #ff6b6b;
        padding: 12px;
        background-color: var(--eerie-black-2);
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        border: 1px solid #ff6b6b30;
    }

    .login-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .input-group {
        position: relative;
    }

    input {
        width: 100%;
        padding: 14px 18px;
        background-color: var(--eerie-black-2);
        border: 1px solid var(--smoky-black-3);
        border-radius: var(--radius-24);
        color: var(--white);
        font-size: 1rem;
        transition: all var(--transition-1);
    }

    input:focus {
        outline: none;
        border-color: var(--gold-crayola);
        box-shadow: 0 0 0 2px var(--gold-crayola);
    }

    .btn-primary {
        width: 100%;
        padding: 16px;
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        border: none;
        border-radius: var(--radius-24);
        font-weight: 700;
        text-transform: uppercase;
        cursor: pointer;
        transition: all var(--transition-1);
        margin-top: 10px;
    }

    .btn-primary:hover {
        background-color: var(--white);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(228, 197, 144, 0.3);
    }

    .material-symbols-outlined {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--quick-silver);
    }
    </style>
</head>
<body>
    <div class="admin-login">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form class="login-form" method="POST" action="login.php">
            <div class="input-group">
                <input type="text" name="username" placeholder="Admin Username" required>
                <span class="material-symbols-outlined">person</span>
            </div>
            
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <span class="material-symbols-outlined">lock</span>
            </div>
            
            <button type="submit" class="btn-primary">Login</button>
        </form>
    </div>
</body>
</html>