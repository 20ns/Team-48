<?php
session_start();
require_once '../db_connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch all menu items
$result = $conn->query("SELECT * FROM menu_items");
$items = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Stock Management</h1>
    <a href="logout.php">Logout</a>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Item Name</th>
            <th>Current Stock</th>
            <th>Action</th>
        </tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= $item['id'] ?></td>
            <td><?= $item['name'] ?></td>
            <td>
                <form method="POST" action="update_stock.php">
                    <input type="number" name="stock" value="<?= $item['stock'] ?>">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <button type="submit">Update</button>
                </form>
            </td>
            <td><?= $item['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>