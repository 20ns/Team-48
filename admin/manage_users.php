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

$search = [];
$queryParams = [];
$whereClauses = [];

try {
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
        /* Table styling */
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin-top: 20px;
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd;
        }
        th { 
            background-color: #f2f2f2; 
        }
        
        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .edit-btn, .delete-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: opacity 0.3s;
        }
        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        .edit-btn:hover, .delete-btn:hover {
            opacity: 0.8;
        }
        
        /* Search form */
        .search-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .search-form input[type="text"] {
            padding: 8px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .search-form button {
            padding: 8px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form a {
            padding: 8px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h1>User Management</h1>
    
    <div class="search-form">
        <form method="GET">
            <input type="text" name="name" placeholder="Search by name" 
                   value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
            <button type="submit">Search</button>
            <a href="manage_users.php">Clear Filters</a>
        </form>
    </div>

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
                <tr><td colspan="7">No users found</td></tr>
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
                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="edit_user.php?user_id=<?= $user['id'] ?>" class="edit-btn">Edit</a>
                            <a href="delete_user.php?user_id=<?= $user['id'] ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone!')">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>