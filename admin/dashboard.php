<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        .nav-buttons { margin-bottom: 20px; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Stock Management</h1>
    
    <div class="nav-buttons">
        <a href="orders.php" class="button">Orders</a>
        <a href="logout.php" class="button">Logout</a>
    </div>
    
    <table>
        <!-- Existing table content remains the same -->
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