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
        .order { border: 1px solid #ddd; margin-bottom: 20px; padding: 15px; }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .success {
            background-color: #dff0d8;
            border: 1px solid #d0e9c6;
            color: #3c763d;
        }

        .error {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
        .order-status-form {
          display: inline-block;
        }

        .order-status-form select {
          padding: 5px;
          border: 1px solid #ccc;
          border-radius: 4px;
          margin-right: 5px;
        }

        .order-status-form button {
          padding: 5px 10px;
          background-color: #4CAF50;
          color: white;
          border: none;
          border-radius: 4px;
          cursor: pointer;
        }

        .order-status-form button:hover {
          background-color: #367c39;
        }
    </style>
</head>
<body>
    <h1>Order Management</h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php while($order = $ordersResult->fetch_assoc()): ?>
        <div class="order">
            <h3>Order #<?= htmlspecialchars($order['id']) ?></h3>
            <p>Customer: <?= htmlspecialchars($order['user_name']) ?> (<?= htmlspecialchars($order['email']) ?>)</p>
            <p>Total: £<?= number_format($order['total'], 2) ?></p>
            <p>Status: 
                <form class="order-status-form" action="update_order.php" method="POST">
                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                    <select name="status">
                        <option value="pending" <?= ($order['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= ($order['status'] == 'processing') ? 'selected' : '' ?>>Processing</option>
                        <option value="completed" <?= ($order['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= ($order['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
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
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>£<?= number_format($item['price'], 2) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td>£<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <?php
                $stmt->close();
                ?>
            </div>
        </div>
    <?php endwhile; ?>
</body>
</html>
