<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../connection.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
    :root {
        --gold-crayola: #e4c590;
        --smoky-black-1: #0A0A0A;
        --smoky-black-2: #1A1A1A;
        --smoky-black-3: #2A2A2A;
        --eerie-black-1: #121212;
        --quick-silver: #A0A0A0;
        --white: #ffffff;
        --radius-24: 24px;
    }

    body {
        background-color: var(--eerie-black-1);
        font-family: 'DM Sans', sans-serif;
        color: var(--white);
    }

    .admin-section {
        padding: 60px 0;
        min-height: 100vh;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    h1 {
        color: var(--gold-crayola);
        font-size: 2.5rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 40px;
        text-align: center;
    }

    .product-form {
        background-color: var(--smoky-black-2);
        border-radius: var(--radius-24);
        padding: 40px;
        border: 1px solid var(--smoky-black-3);
        box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        max-width: 800px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 12px;
        color: var(--gold-crayola);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-size: 0.9rem;
    }

    input, textarea, select {
        width: 100%;
        padding: 14px 20px;
        background-color: var(--smoky-black-3);
        border: 1px solid var(--gold-crayola);
        border-radius: var(--radius-24);
        color: var(--white);
        font-size: 1rem;
        font-family: inherit;
        transition: all 0.3s ease;
    }

    input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: var(--white);
        box-shadow: 0 0 0 3px rgba(228, 197, 144, 0.2);
    }

    input[type="file"] {
        padding: 10px;
        cursor: pointer;
    }

    .btn-secondary {
        display: block;
        width: 100%;
        padding: 16px;
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        border: none;
        border-radius: var(--radius-24);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .btn-secondary:hover {
        background-color: var(--smoky-black-3);
        color: var(--gold-crayola);
        transform: translateY(-2px);
    }

    .back-to-dashboard {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background-color: var(--smoky-black-2);
        color: var(--gold-crayola);
        text-decoration: none;
        border-radius: var(--radius-24);
        transition: all 0.3s ease;
        border: 1px solid var(--gold-crayola);
        margin: 30px auto 0;
        font-weight: 500;
    }

    .back-to-dashboard:hover {
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        transform: translateY(-2px);
    }

    .alert {
        padding: 15px 25px;
        border-radius: var(--radius-24);
        margin: 25px 0;
        font-weight: 700;
    }

    .error {
        background-color: #4d2a2a;
        color: #ff6b6b;
        border: 1px solid #a94442;
    }

    .success {
        background-color: #2a4d2e;
        color: var(--gold-crayola);
        border: 1px solid #3c763d;
    }
    </style>
</head>
<body>
    <section class="admin-section">
        <div class="dashboard-container">
            <h1>Add New Product</h1>
            
            <form class="product-form" id="menuItemForm" enctype="multipart/form-data" method="POST" action="addtomenu.php">
                <div class="form-group">
                    <label for="itemName">Item Name</label>
                    <input type="text" id="itemName" name="itemName" required>
                </div>

                <div class="form-group">
                    <label for="itemDescription">Item Description</label>
                    <textarea id="itemDescription" name="itemDescription" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="itemPrice">Item Price (£)</label>
                    <input type="number" id="itemPrice" name="itemPrice" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="itemImage">Item Image</label>
                    <input type="file" id="itemImage" name="itemImage" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label for="itemCategory">Category</label>
                    <select id="itemCategory" name="itemCategory" required>
                        <option value="dessert">Dessert</option>
                        <option value="starter">Starter</option>
                        <option value="drink">Drink</option>
                        <option value="sides">Sides</option>
                        <option value="main">Main</option>
                    </select>
                </div>

                <button type="submit" name="addtomenu" class="btn-secondary">Add Product</button>
            </form>

            <a href="dashboard.php" class="back-to-dashboard">
                ← Back to Dashboard
            </a>
        </div>
    </section>
</body>
</html>