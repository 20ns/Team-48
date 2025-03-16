<?php
require 'connection.php';
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Search functionality
$search = [];
$queryParams = [];
$whereClauses = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search'])) {
    $search = [
        'name' => $_GET['name'] ?? '',
        'email' => $_GET['email'] ?? '',
        'phone' => $_GET['phone'] ?? ''
    ];
    
    // Build WHERE clauses
    if (!empty($search['name'])) {
        $whereClauses[] = "users.name LIKE :name";
        $queryParams[':name'] = '%' . $search['name'] . '%';
    }
    if (!empty($search['email'])) {
        $whereClauses[] = "users.email LIKE :email";
        $queryParams[':email'] = '%' . $search['email'] . '%';
    }
    if (!empty($search['phone'])) {
        $whereClauses[] = "users.phone_number LIKE :phone";
        $queryParams[':phone'] = '%' . $search['phone'] . '%';
    }
}

try {
    // Base query
    $sql = "
        SELECT users.id, users.name, users.email, users.phone_number, 
               userinfo.addressLine1, userinfo.city, userinfo.postalCode,
               users.created_at
        FROM users 
        LEFT JOIN userinfo ON users.id = userinfo.userID
    ";
    
    // Add WHERE clauses if any
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }
    
    $sql .= " ORDER BY users.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($queryParams);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <style>
        /* Existing styles remain */
        .danger-btn { background-color: #dc3545; color: white; padding: 5px 10px; text-decoration: none; }
        .actions { display: flex; gap: 10px; }
    </style>
</head>
<body>
    <h1>User Management</h1>
    
    <!-- Search Form -->
    <form class="search-form" method="GET">
        <!-- Existing search fields -->
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
            <tr>
                <td colspan="7">No users found</td>
            </tr>
            <?php endif; ?>
            
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
                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                <td>
                    <div class="actions">
                        <a href="edit_user.php?user_id=<?= $user['id'] ?>" class="edit-btn">Edit</a>
                        <a href="delete_user.php?user_id=<?= $user['id'] ?>" 
                        class="danger-btn" 
                        nclick="return confirm('Are you sure? This cannot be undone!')">Delete</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>