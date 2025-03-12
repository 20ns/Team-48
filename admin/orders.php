<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once '../connection.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Get all orders with user information
try {
    $ordersQuery = "
        SELECT o.*, u.name AS user_name, u.email 
        FROM orderDetails o 
        JOIN users u ON o.userid = u.id 
        ORDER BY o.created_at DESC
    ";
    $ordersResult = $pdo->query($ordersQuery); // Use $pdo, not $conn

} catch (PDOException $e){
    die("Error fetching orders: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Management</title>
    <style>
        /* Your existing CSS */
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
        .back-to-dashboard {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff; /* Blue color */
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .back-to-dashboard:hover {
           background-color: #0056b3; /* Darker blue on hover */
         }

    </style>
</head>
<body>
    <h1>Order Management</h1>
     <a href="dashboard.php" class="back-to-dashboard">Back to Dashboard</a>


    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php while($order = $ordersResult->fetch(PDO::FETCH_ASSOC)): ?>
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
                // Get order items using PDO
                try{
                    $itemsQuery = "
                        SELECT p.name, p.price, oi.quantity 
                        FROM orderItems oi 
                        JOIN product p ON oi.productID = p.id 
                        WHERE oi.orderID = ?
                    ";
                    $stmt = $pdo->prepare($itemsQuery);
                    $stmt->execute([$order['id']]);
                    $itemsResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e){
                    echo "Error: " . $e->getMessage();
                }

                ?>

                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($itemsResult as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td>£<?= number_format($item['price'], 2) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td>£<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endwhile; ?>
</body>
</html>