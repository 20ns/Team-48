<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require $_SERVER['DOCUMENT_ROOT'] . '/connection.php';

if (!isset($_GET['user_id'])) {
    header('Location: manage_users.php');
    exit;
}

$userId = $_GET['user_id'];

try {
    $pdo->beginTransaction();
    
    // Delete cart items
    $stmt = $pdo->prepare("DELETE FROM cartItem WHERE sessionID IN (SELECT id FROM shoppingSession WHERE userID = ?)");
    $stmt->execute([$userId]);
    
    // Delete shopping sessions
    $stmt = $pdo->prepare("DELETE FROM shoppingSession WHERE userID = ?");
    $stmt->execute([$userId]);
    
    // Delete user info
    $stmt = $pdo->prepare("DELETE FROM userinfo WHERE userID = ?");
    $stmt->execute([$userId]);
    
    // Finally delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    
    $pdo->commit();
    
    header('Location: manage_users.php?deleted=1');
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    die("Error deleting user: " . $e->getMessage());
}