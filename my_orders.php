<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['userID'])) {
    header("Location: logIn.php");
    exit();
}

$userID = $_SESSION['userID'];
if (isset($_POST['cancel_order_id'])) {
    $orderID = $_POST['cancel_order_id'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT total FROM orderDetails WHERE id = ? AND userid = ? AND status != 'completed'");
        $stmt->execute([$orderID, $userID]);

        if ($stmt->rowCount() > 0) {
            $getItems = $pdo->prepare("SELECT productID, quantity FROM orderItems WHERE orderID = ?");
            $getItems->execute([$orderID]);
            $itemsToRestore = $getItems->fetchAll(PDO::FETCH_ASSOC);
            foreach ($itemsToRestore as $item) {
                $updateStock = $pdo->prepare("UPDATE product SET stock = stock + ? WHERE id = ?");
                $updateStock->execute([$item['quantity'], $item['productID']]);
            }
            $deleteItems = $pdo->prepare("DELETE FROM orderItems WHERE orderID = ?");
            $deleteItems->execute([$orderID]);
            $deleteOrder = $pdo->prepare("DELETE FROM orderDetails WHERE id = ?");
            $deleteOrder->execute([$orderID]);
            $pdo->commit();
            $_SESSION['message'] = "Order #$orderID has been canceled and stock restored.";
        } else {
            $_SESSION['message'] = "Unable to cancel the order.";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = "Error canceling order: " . $e->getMessage();
    }
    header("Location: my_orders.php");
    exit();
}

// Get orders
$stmt = $pdo->prepare("
    SELECT od.id, od.total, od.status, od.created_at, oi.productID, oi.quantity, oi.price, p.name 
    FROM orderDetails od
    JOIN orderItems oi ON od.id = oi.orderID
    JOIN product p ON oi.productID = p.id
    WHERE od.userid = ?
    ORDER BY od.created_at DESC
");
$stmt->execute([$userID]);
$orders = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $orders[$row['id']]['details'] = [
        'total' => $row['total'],
        'status' => $row['status'],
        'created_at' => $row['created_at']
    ];
    $orders[$row['id']]['items'][] = [
        'product_name' => $row['name'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Peri Palace</title>
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

    .container {
        max-width: 1200px;
        margin: 0 auto;
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

    .order h2 {
        color: var(--gold-crayola);
        margin-bottom: 15px;
        font-size: 1.8rem;
    }

    .order p {
        color: var(--quick-silver);
        margin: 10px 0;
        font-size: 1.1rem;
    }

    .order h3 {
        color: var(--gold-crayola);
        margin: 20px 0 15px;
        font-size: 1.4rem;
    }

    .order ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .order li {
        background-color: var(--smoky-black-3);
        padding: 15px 20px;
        margin-bottom: 10px;
        border-radius: var(--radius-24);
        display: flex;
        justify-content: space-between;
        align-items: center;
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

    button[type="submit"] {
        padding: 12px 24px;
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        border: none;
        border-radius: var(--radius-24);
        cursor: pointer;
        font-weight: 700;
        transition: all var(--transition-1);
        margin-top: 20px;
    }

    button[type="submit"]:hover {
        background-color: var(--white);
        transform: translateY(-2px);
    }

    .no-orders {
        text-align: center;
        color: var(--quick-silver);
        font-size: 1.2rem;
        padding: 40px 0;
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Orders</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert <?= strpos($_SESSION['message'], 'canceled') !== false ? 'success' : 'error' ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
            <!-- Fetch orders and display them -->
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $orderID => $order): ?>
                <div class="order">
                    <h2>Order #<?= htmlspecialchars($orderID) ?></h2>
                    <p>Total: £<?= number_format($order['details']['total'], 2) ?></p>
                    <p>Status: <span class="status"><?= htmlspecialchars($order['details']['status']) ?></span></p>
                    <p>Placed on: <?= date('d/m/Y H:i', strtotime($order['details']['created_at'])) ?></p>

                    <h3>Items:</h3>
                    <ul>
                        <?php foreach ($order['items'] as $item): ?>
                            <li>
                                <span><?= htmlspecialchars($item['product_name']) ?></span>
                                <span>Qty: <?= htmlspecialchars($item['quantity']) ?></span>
                                <span>£<?= number_format($item['price'], 2) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($order['details']['status'] != 'completed'): ?>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                            <input type="hidden" name="cancel_order_id" value="<?= htmlspecialchars($orderID) ?>">
                            <button type="submit">Cancel Order</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-orders">You have no orders yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>