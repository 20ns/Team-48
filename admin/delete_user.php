<?php
require 'connection.php';
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['user_id'])) {
    header('Location: manage_users.php');
    exit;
}

$userId = $_GET['user_id'];

try {
    $pdo->beginTransaction();
    
    // Delete related records
    $stmt = $pdo->prepare("DELETE FROM cartItem WHERE sessionID IN (SELECT id FROM shoppingSession WHERE userID = ?)");
    $stmt->execute([$userId]);
    
    $stmt = $pdo->prepare("DELETE FROM shoppingSession WHERE userID = ?");
    $stmt->execute([$userId]);
    
    $stmt = $pdo->prepare("DELETE FROM userinfo WHERE userID = ?");
    $stmt->execute([$userId]);
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    
    $pdo->commit();
    
    header('Location: manage_users.php?deleted=1');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    die("Error deleting user: " . $e->getMessage());
}