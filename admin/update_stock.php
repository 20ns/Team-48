<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemID = $_POST['item_id'] ?? null;
    $newStock = $_POST['stock'] ?? null;

    if ($itemID === null || $newStock === null) {
        die("Error: Missing item ID or stock value.");
    }

    // Validate input (VERY important to prevent SQL injection)
    $itemID = (int)$itemID;  // Force to integer
    $newStock = (int)$newStock; // Force to integer

    // Check if the item ID is valid (exists in the database)
    try {
        $checkStmt = $pdo->prepare("SELECT 1 FROM product WHERE id = ?");
        $checkStmt->execute([$itemID]);
        if (!$checkStmt->fetch()) {
            die("Error: Invalid item ID.");
        }


        $stmt = $pdo->prepare("UPDATE product SET stock = :stock WHERE id = :id");
        $stmt->bindParam(':stock', $newStock, PDO::PARAM_INT);
        $stmt->bindParam(':id', $itemID, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: dashboard.php');
        exit;

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
} else {
    header('Location: dashboard.php');
    exit;
}
?>