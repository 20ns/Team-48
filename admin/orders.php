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
        padding: 40px 20px;
        min-height: 100vh;
    }

    h1 {
        color: var(--gold-crayola);
        margin-bottom: 40px;
        font-size: 2.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .order {
        background-color: var(--smoky-black-2);
        border-radius: var(--radius-24);
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        border: 1px solid var(--smoky-black-3);
    }

    .order h3 {
        color: var(--gold-crayola);
        margin-bottom: 15px;
        font-size: 1.5rem;
    }

    .order p {
        color: var(--quick-silver);
        margin: 10px 0;
    }

    .order-items {
        background-color: var(--smoky-black-3);
        border-radius: var(--radius-24);
        padding: 20px;
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: var(--smoky-black-2);
        border-radius: var(--radius-24);
        overflow: hidden;
        margin: 20px 0;
    }

    th, td {
        padding: 15px 20px;
        border-bottom: 1px solid var(--eerie-black-2);
        text-align: left;
    }

    th {
        background-color: var(--smoky-black-3);
        color: var(--gold-crayola);
        font-weight: 700;
        text-transform: uppercase;
    }

    tr:hover {
        background-color: var(--smoky-black-3);
    }

    .alert {
        padding: 15px 25px;
        border-radius: var(--radius-24);
        margin: 25px 0;
        font-weight: 700;
    }

    .success {
        background-color: #2a4d2e;
        color: var(--gold-crayola);
        border: 1px solid #3c763d;
    }

    .error {
        background-color: #4d2a2a;
        color: #ff6b6b;
        border: 1px solid #a94442;
    }

    .back-to-dashboard {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background-color: var(--smoky-black-3);
        color: var(--gold-crayola);
        text-decoration: none;
        border-radius: var(--radius-24);
        transition: all var(--transition-1);
        border: 1px solid var(--gold-crayola);
        margin-bottom: 30px;
    }

    .back-to-dashboard:hover {
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        transform: translateY(-2px);
    }

    .order-status-form {
        display: inline-flex;
        gap: 10px;
        align-items: center;
    }

    .order-status-form select {
        padding: 8px 16px;
        background-color: var(--eerie-black-2);
        border: 1px solid var(--smoky-black-3);
        border-radius: var(--radius-24);
        color: var(--white);
        font-family: inherit;
    }

    .order-status-form button {
        padding: 8px 16px;
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        border: none;
        border-radius: var(--radius-24);
        cursor: pointer;
        font-weight: 700;
        transition: all var(--transition-1);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .order-status-form button:hover {
        background-color: var(--white);
        transform: translateY(-2px);
    }

    .material-symbols-outlined {
        font-size: 18px;
        vertical-align: middle;
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
                <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                
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