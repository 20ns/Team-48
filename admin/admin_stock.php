<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../connection.php';

try {
    $stmt = $pdo->query("SELECT id, name, stock FROM product");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management - Admin</title>

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
    }

    .admin-section {
        padding: 40px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    h1 {
        font-size: 2.5rem;
        color: var(--gold-crayola);
        margin-bottom: 40px;
        text-align: center;
    }

    .nav-buttons {
        margin-bottom: 30px;
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .admin-btn {
        display: inline-flex;
        padding: 12px 24px;
        background-color: var(--smoky-black-3);
        color: var(--gold-crayola);
        font-weight: 700;
        text-transform: uppercase;
        border-radius: var(--radius-24);
        text-decoration: none;
        transition: all var(--transition-1);
        border: 2px solid var(--gold-crayola);
    }

    .admin-btn:hover {
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        transform: translateY(-2px);
    }

    .stock-table {
        width: 100%;
        border-collapse: collapse;
        margin: 40px 0;
        background-color: var(--smoky-black-2);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border-radius: var(--radius-24);
        overflow: hidden;
    }

    .stock-table th,
    .stock-table td {
        padding: 18px 25px;
        text-align: left;
    }

    .stock-table th {
        background-color: var(--smoky-black-3);
        color: var(--gold-crayola);
        font-weight: 700;
        text-transform: uppercase;
    }

    .stock-table tr {
        border-bottom: 1px solid var(--eerie-black-2);
    }

    .stock-table tr:last-child {
        border-bottom: none;
    }

    .stock-table tr:hover {
        background-color: var(--smoky-black-3);
    }

    input[type="number"] {
        padding: 8px 12px;
        background-color: var(--eerie-black-2);
        border: 1px solid var(--quick-silver);
        border-radius: 8px;
        color: var(--white);
        width: 100px;
        margin-right: 10px;
    }

    .update-btn {
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        border: none;
        border-radius: var(--radius-24);
        padding: 8px 20px;
        cursor: pointer;
        font-weight: 700;
        transition: all var(--transition-1);
    }

    .update-btn:hover {
        background-color: var(--white);
        transform: scale(1.05);
    }

    .stock-status {
        font-weight: 700;
        text-transform: uppercase;
    }
    </style>
</head>
<body>
    <section class="admin-section">
        <h1>Stock Management</h1>

        <div class="nav-buttons">
            <a href="dashboard.php" class="admin-btn">Dashboard</a>
            <a href="orders.php" class="admin-btn">Orders</a>
            <a href="logout.php" class="admin-btn">Logout</a>
        </div>

        <table class="stock-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Current Stock</th>
                    <th>Stock Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['id']) ?></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['stock']) ?></td>
                    <td>
                        <span class="stock-status" style="color: <?= $item['stock'] > 0 ? 'var(--gold-crayola)' : '#ff4757' ?>;">
                            <?= $item['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="update_stock.php">
                            <input type="number" name="stock" value="<?= htmlspecialchars($item['stock']) ?>">
                            <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                            <button type="submit" class="update-btn">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</body>
</html>