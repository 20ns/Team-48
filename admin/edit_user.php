<?php
require 'connection.php';
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$user = [];
$error = '';
$success = '';

// Fetch user data
if (isset($_GET['user_id'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT users.id, users.name, users.email, users.phone_number, 
                   userinfo.addressLine1, userinfo.city, userinfo.postalCode 
            FROM users 
            LEFT JOIN userinfo ON users.id = userinfo.userID 
            WHERE users.id = ?
        ");
        $stmt->execute([$_GET['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $error = "User not found.";
        }
    } catch (PDOException $e) {
        $error = "Error fetching user: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    try {
        $pdo->beginTransaction();

        // Update users table
        $stmt = $pdo->prepare("
            UPDATE users 
            SET name = ?, email = ?, phone_number = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['phone_number'],
            $_POST['user_id']
        ]);

        // Update or insert userinfo
        $stmt = $pdo->prepare("
            INSERT INTO userinfo (userID, addressLine1, city, postalCode)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                addressLine1 = VALUES(addressLine1),
                city = VALUES(city),
                postalCode = VALUES(postalCode)
        ");
        $stmt->execute([
            $_POST['user_id'],
            $_POST['address'],
            $_POST['city'],
            $_POST['postal_code']
        ]);

        $pdo->commit();
        header("Location: edit_user.php?user_id=" . $_POST['user_id'] . "&success=1");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Error updating user: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Existing styles -->
</head>
<body>
    <div class="container">
        <!-- Existing form -->
    </div>
</body>
</html>