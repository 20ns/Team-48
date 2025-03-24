<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin_loggedin'])) { 
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Get current status first using PDO
        $currentStatusStmt = $pdo->prepare("SELECT status FROM orderDetails WHERE id = ?");
        $currentStatusStmt->execute([$order_id]);
        $currentStatus = $currentStatusStmt->fetchColumn(); 

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
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
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

    } catch (PDOException $e) { 
        $pdo->rollBack();
        error_log("Order update failed: " . $e->getMessage());
        $_SESSION['error'] = "Failed to update order status";

    }

    header("Location: orders.php");
    exit();
}