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
    }

    .admin-section {
        padding: 40px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .dashboard-container {
        max-width: 800px;
        margin: 50px auto;
        background-color: var(--smoky-black-2);
        padding: 40px;
        border-radius: var(--radius-24);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        animation: fadeIn 0.5s ease-in-out;
        border: 1px solid var(--smoky-black-3);
    }

    h1 {
        color: var(--gold-crayola);
        margin-bottom: 30px;
        font-size: 2.5rem;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .welcome-message {
        color: var(--quick-silver);
        font-size: 1.2rem;
        margin-bottom: 40px;
    }

    .button {
        display: inline-flex;
        padding: 16px 32px;
        background-color: var(--smoky-black-3);
        color: var(--gold-crayola);
        font-weight: 700;
        text-transform: uppercase;
        border-radius: var(--radius-24);
        text-decoration: none;
        transition: all var(--transition-1);
        border: 2px solid var(--gold-crayola);
        min-width: 200px;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    .button:hover {
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(228, 197, 144, 0.3);
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 40px 0;
        display: grid;
        gap: 20px;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        justify-content: center;
    }

    ul li {
        display: flex;
        justify-content: center;
    }

    @keyframes fadeIn {
        from { 
            opacity: 0; 
            transform: translateY(-20px) scale(0.95);
        }
        to { 
            opacity: 1; 
            transform: translateY(0) scale(1);
        }
    }
    </style>
</head>
<body>
    <section class="admin-section">
        <div class="dashboard-container">
            <h1>Admin Dashboard</h1>
            <p class="welcome-message">Welcome, Admin!</p>

            <ul>
                <li><a href="admin_stock.php" class="button">
                    <span class="material-symbols-outlined">inventory</span>
                    Manage Stock
                </a></li>
                <li><a href="orders.php" class="button">
                    <span class="material-symbols-outlined">receipt</span>
                    View Orders
                </a></li>
                <li><a href="manage_users.php" class="button">
                    <span class="material-symbols-outlined">group</span>
                    Manage Users
                </a></li>
                <li><a href="add_product.php" class="button">
                    <span class="material-symbols-outlined">add_box</span>
                    Add Product
                </a></li>
                <li><a href="logout.php" class="button">
                    <span class="material-symbols-outlined">logout</span>
                    Logout
                </a></li>
            </ul>
        </div>
    </section>
</body>
</html>