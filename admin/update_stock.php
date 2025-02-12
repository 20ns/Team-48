<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $stock = (int)$_POST['stock'];

    $stmt = $conn->prepare("UPDATE menu_items SET stock = ? WHERE id = ?");
    $stmt->bind_param("ii", $stock, $item_id);
    $stmt->execute();
    
    header("Location: dashboard.php");
    exit();
}
?>