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
    <style>
        body {
            font-family: sans-serif;
        }
        .button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 10px;
            display: inline-block;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin!</p>

    <ul>
        <li><a href="admin_stock.php" class="button">Manage Stock</a></li>
        <li><a href="orders.php" class="button">View Orders</a></li>
        <li><a href="logout.php" class="button">Logout</a></li>
    </ul>
</body>
</html>