<?php
session_start();
require_once 'connection.php'; // Uses PDO, not MySQLi

if (!isset($_SESSION['userID'])) {
    header("Location: logIn.php");
    exit();
}

$userID = $_SESSION['userID'];

// Handle order cancellation
if (isset($_POST['cancel_order_id'])) {
    $orderID = $_POST['cancel_order_id'];

    try {
        $pdo->beginTransaction();
        
        // Check if order can be canceled
        $stmt = $pdo->prepare("SELECT total FROM orderDetails WHERE id = ? AND userid = ? AND status != 'completed'");
        $stmt->execute([$orderID, $userID]);
        
        if ($stmt->rowCount() > 0) {
            // Delete order items
            $deleteItems = $pdo->prepare("DELETE FROM orderItems WHERE orderID = ?");
            $deleteItems->execute([$orderID]);
            
            // Delete order
            $deleteOrder = $pdo->prepare("DELETE FROM orderDetails WHERE id = ?");
            $deleteOrder->execute([$orderID]);
            
            $pdo->commit();
            $message = "Order #$orderID has been canceled.";
        } else {
            $message = "Unable to cancel the order.";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "Error canceling order: " . $e->getMessage();
    }
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
    <title>My Orders</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>My Orders</h1>
        <?php if (isset($message)) echo "<p>$message</p>"; ?>
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $orderID => $order): ?>
                <div class="order">
                    <h2>Order #<?= htmlspecialchars($orderID) ?></h2>
                    <p>Total: £<?= number_format($order['details']['total'], 2) ?></p>
                    <p>Status: <?= htmlspecialchars($order['details']['status']) ?></p>
                    <p>Placed on: <?= htmlspecialchars($order['details']['created_at']) ?></p>
                    <h3>Items:</h3>
                    <ul>
                        <?php foreach ($order['items'] as $item): ?>
                            <li>
                                <?= htmlspecialchars($item['product_name']) ?> - 
                                Quantity: <?= htmlspecialchars($item['quantity']) ?> - 
                                Price: £<?= number_format($item['price'], 2) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($order['details']['status'] != 'completed'): ?>
                        <form method="POST">
                            <input type="hidden" name="cancel_order_id" value="<?= htmlspecialchars($orderID) ?>">
                            <button type="submit">Cancel Order</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no orders.</p>
        <?php endif; ?>
    </div>
</body>
</html>