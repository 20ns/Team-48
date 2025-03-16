<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Font Links -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

    <!-- Link to existing CSS -->
    <link rel="stylesheet" href="./assets/css/style.css">

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--white-alpha-10);
            color: var(--smoky-black);
            text-align: center;
        }

        .dashboard-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: var(--white);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 10px hsla(0, 0%, 0%, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        h1 {
            color: var(--smoky-black);
        }

        .button {
            display: inline-block;
            padding: 12px 18px;
            background-color: var(--gold-fusion);
            color: var(--white);
            font-weight: bold;
            text-transform: uppercase;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: var(--davys-grey);
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        ul li {
            display: inline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, Admin!</p>

        <ul>
            <li><a href="admin_stock.php" class="button">Manage Stock</a></li>
            <li><a href="orders.php" class="button">View Orders</a></li>
            <li><a href="manage_users.php" class="button">Manage Users</a></li>
    		<li><a href="logout.php" class="button">Logout</a></li>
        </ul>
    </div>
</body>
</html>