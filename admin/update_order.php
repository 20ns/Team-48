<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin_loggedin'])) {  // Corrected session variable name
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Start transaction
    $pdo->beginTransaction();  // Use $pdo for PDO transactions

    try {
        // Get current status first using PDO
        $currentStatusStmt = $pdo->prepare("SELECT status FROM orderDetails WHERE id = ?");
        $currentStatusStmt->execute([$order_id]);
        $currentStatus = $currentStatusStmt->fetchColumn();  // Fetch single value

        // Update order status using PDO
        $updateStmt = $pdo->prepare("UPDATE orderDetails SET status = ? WHERE id = ?");
        $updateStmt->execute([$status, $order_id]);


        // Restore stock if changing to cancelled using PDO
        if ($status === 'cancelled' && $currentStatus !== 'cancelled') {
            $itemsStmt = $pdo->prepare("
                SELECT productID, quantity 
                FROM orderItems 
                WHERE orderID = ?
            ");
            $itemsStmt->execute([$order_id]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all items

            foreach ($items as $item) {
                // Update product stock using PDO
                $restoreStmt = $pdo->prepare("
                    UPDATE product 
                    SET stock = stock + ? 
                    WHERE id = ?
                ");
                $restoreStmt->execute([$item['quantity'], $item['productID']]);
            }
        }

        $pdo->commit();
        $_SESSION['success'] = "Order status updated successfully";

    } catch (PDOException $e) {  // Catch PDOException
        $pdo->rollBack();
        error_log("Order update failed: " . $e->getMessage());
        $_SESSION['error'] = "Failed to update order status";

    }

    header("Location: orders.php");
    exit();
}