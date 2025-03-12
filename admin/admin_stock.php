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
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        /* Your existing CSS styles */
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .nav-buttons {
            margin-bottom: 20px;
        }
        .button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 10px;
        }
        .button:hover {
            background-color: #3e8e41;
        }
    </style>
</head>
<body>
    <h1>Stock Management</h1>

    <div class="nav-buttons">
    <!--Links back to the dashboard-->
        <a href="dashboard.php" class="button">Dashboard</a>
        <a href="orders.php" class="button">Orders</a>
        <a href="logout.php" class="button">Logout</a>
    </div>

    <table>
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
                <td style="color: <?= $item['stock'] > 0 ? 'green' : 'red' ?>;">
                    <?= $item['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                </td>
                <td>
                    <form method="POST" action="update_stock.php">
                        <input type="number" name="stock" value="<?= htmlspecialchars($item['stock']) ?>">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>