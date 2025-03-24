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

        // Update userinfo
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
            $_POST['address'] ?? '',
            $_POST['city'] ?? '',
            $_POST['postal_code'] ?? ''
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
    <meta charset="UTF-8">
    <title>Edit User</title>
    <style>
        .container { max-width: 600px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; }
        .success { color: green; }
        .error { color: red; }
        button { padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <p class="success">User updated successfully!</p>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($user): ?>
        <form method="POST">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
            
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Address:</label>
                <input type="text" name="address" value="<?= htmlspecialchars($user['addressLine1']) ?>">
            </div>
            
            <div class="form-group">
                <label>City:</label>
                <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>">
            </div>
            
            <div class="form-group">
                <label>Postal Code:</label>
                <input type="text" name="postal_code" value="<?= htmlspecialchars($user['postalCode']) ?>">
            </div>
            
            <button type="submit" name="update_user">Update User</button>
            <a href="manage_users.php">Back to Users</a>
        </form>
        <?php else: ?>
            <p class="error">User not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>