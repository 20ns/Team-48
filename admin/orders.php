<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get all orders with user information
$ordersQuery = "
    SELECT o.*, u.name AS user_name, u.email 
    FROM orderDetails o 
    JOIN users u ON o.userid = u.id 
    ORDER BY o.created_at DESC
";
$ordersResult = $conn->query($ordersQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Management</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .order-items { margin: 10px 0; padding: 10px; background: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Order Management</h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <?php while($order = $ordersResult->fetch_assoc()): ?>
        <div class="order">
            <h3>Order #<?= $order['id'] ?></h3>
            <p>Customer: <?= $order['user_name'] ?> (<?= $order['email'] ?>)</p>
            <p>Total: £<?= number_format($order['total'], 2) ?></p>
            <p>Status: 
                <form action="update_order.php" method="POST" style="display: inline;">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status">
                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    <button type="submit">Update Status</button>
                </form>
            </p>
            <p>Order Date: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>

            <div class="order-items">
                <h4>Items:</h4>
                <?php
                // Get order items
                $itemsQuery = "
                    SELECT p.name, p.price, oi.quantity 
                    FROM orderItems oi 
                    JOIN product p ON oi.productID = p.id 
                    WHERE oi.orderID = ?
                ";
                $stmt = $conn->prepare($itemsQuery);
                $stmt->bind_param("i", $order['id']);
                $stmt->execute();
                $itemsResult = $stmt->get_result();
                ?>

                <table>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                    <?php while($item = $itemsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $item['name'] ?></td>
                            <td>£<?= number_format($item['price'], 2) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>£<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    <?php endwhile; ?>
</body>
</html>