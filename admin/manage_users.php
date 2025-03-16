<?php
// Error reporting at the VERY TOP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Session must start before any output
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require 'connection.php'; // Moved after session_start()

// Simplified search functionality
$search = [];
$queryParams = [];
$whereClauses = [];

try {
    // Base query matching your exact table structure
    $sql = "
        SELECT 
            users.id, 
            users.name, 
            users.email, 
            users.phone_number, 
            userinfo.addressLine1, 
            userinfo.city, 
            userinfo.postalCode,
            users.created_at
        FROM users
        LEFT JOIN userinfo ON users.id = userinfo.userID
    ";

    // Add search filters if provided
    if (!empty($_GET['name'])) {
        $sql .= " WHERE users.name LIKE ?";
        $queryParams[] = '%' . $_GET['name'] . '%';
    }

    $sql .= " ORDER BY users.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($queryParams);
    $users = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <style>
        /* Simple table styling */
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>User Management</h1>
    
    <!-- Simple Search Form -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="name" placeholder="Search by name" 
               value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
        <button type="submit">Search</button>
        <a href="manage_users.php">Clear</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="6">No users found</td></tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone_number']) ?></td>
                    <td>
                        <?= htmlspecialchars($user['addressLine1']) ?><br>
                        <?= htmlspecialchars($user['city']) ?> 
                        <?= htmlspecialchars($user['postalCode']) ?>
                    </td>
                    <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>