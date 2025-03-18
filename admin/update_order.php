<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get current status first
        $currentStatusStmt = $conn->prepare("SELECT status FROM orderDetails WHERE id = ?");
        $currentStatusStmt->bind_param("i", $order_id);
        $currentStatusStmt->execute();
        $currentStatus = $currentStatusStmt->get_result()->fetch_assoc()['status'];
        $currentStatusStmt->close();

        // Update order status
        $updateStmt = $conn->prepare("UPDATE orderDetails SET status = ? WHERE id = ?");
        $updateStmt->bind_param("si", $status, $order_id);
        $updateStmt->execute();
        $updateStmt->close();

        // Restore stock if changing to cancelled
        if ($status === 'cancelled' && $currentStatus !== 'cancelled') {
            $itemsStmt = $conn->prepare("
                SELECT productID, quantity 
                FROM orderItems 
                WHERE orderID = ?
            ");
            $itemsStmt->bind_param("i", $order_id);
            $itemsStmt->execute();
            $itemsResult = $itemsStmt->get_result();
            
            while ($item = $itemsResult->fetch_assoc()) {
                // Update product stock
                $restoreStmt = $conn->prepare("
                    UPDATE product 
                    SET stock = stock + ? 
                    WHERE id = ?
                ");
                $restoreStmt->bind_param("ii", $item['quantity'], $item['productID']);
                $restoreStmt->execute();
                $restoreStmt->close();
            }
            $itemsStmt->close();
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Order update failed: " . $e->getMessage());
        $_SESSION['error'] = "Failed to update order status";
        header("Location: orders.php");
        exit();
    }

    $_SESSION['success'] = "Order status updated successfully";
    header("Location: orders.php");
    exit();
}