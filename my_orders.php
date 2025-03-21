<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['userID'])) {
    header("Location: logIn.php");
    exit();
}

$userID = $_SESSION['userID'];

// Handle order cancellation
if (isset($_POST['cancel_order_id'])) {
    $orderID = $_POST['cancel_order_id'];

    $stmt = $conn->prepare("SELECT total FROM orderDetails WHERE id = ? AND userid = ? AND status != 'completed'");
    $stmt->bind_param("ii", $orderID, $userID);
    $stmt->execute();
    $stmt->bind_result($orderTotal);
    if ($stmt->fetch()) {
        $stmt->close();

        // Delete the order and associated items
        $conn->query("DELETE FROM orderItems WHERE orderID = $orderID");
        $conn->query("DELETE FROM orderDetails WHERE id = $orderID");
        $message = "Order #$orderID has been canceled.";
    } else {
        $message = "Unable to cancel the order.";
    }
    $stmt->close();
}

// Fetch user's orders
$stmt = $conn->prepare("
    SELECT od.id, od.total, od.status, od.created_at, oi.productID, oi.quantity, oi.price, p.name 
    FROM orderDetails od
    JOIN orderItems oi ON od.id = oi.orderID
    JOIN product p ON oi.productID = p.id
    WHERE od.userid = ?
    ORDER BY od.created_at DESC
");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
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
$stmt->close();
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