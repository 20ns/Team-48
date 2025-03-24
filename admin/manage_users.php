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
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Manage Users</title>
     <style>
    :root {
        --gold-crayola: #e4c590;
        --smoky-black-1: #0A0A0A;
        --smoky-black-2: #1A1A1A;
        --smoky-black-3: #2A2A2A;
        --eerie-black-1: #121212;
        --eerie-black-2: #1E1E1E;
        --quick-silver: #A0A0A0;
        --white: #ffffff;
        --radius-24: 24px;
        --transition-1: 0.25s ease;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        background-color: var(--eerie-black-1);
        color: var(--white);
        padding: 40px 20px;
        min-height: 100vh;
    }

    h1 {
        color: var(--gold-crayola);
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .search-form {
        background-color: var(--smoky-black-2);
        padding: 25px;
        border-radius: var(--radius-24);
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        border: 1px solid var(--smoky-black-3);
    }

    .search-form input[type="text"] {
        padding: 12px 18px;
        background-color: var(--eerie-black-2);
        border: 1px solid var(--smoky-black-3);
        border-radius: var(--radius-24);
        color: var(--white);
        width: 300px;
        transition: all var(--transition-1);
    }

    .search-form input[type="text"]:focus {
        border-color: var(--gold-crayola);
        box-shadow: 0 0 0 2px var(--gold-crayola);
        outline: none;
    }

    .search-form button {
        padding: 12px 24px;
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        border: none;
        border-radius: var(--radius-24);
        cursor: pointer;
        font-weight: 700;
        transition: all var(--transition-1);
        margin-left: 10px;
    }

    .search-form button:hover {
        background-color: var(--white);
        transform: translateY(-2px);
    }

    .search-form a {
        padding: 12px 24px;
        background-color: var(--smoky-black-3);
        color: var(--quick-silver);
        border-radius: var(--radius-24);
        margin-left: 10px;
        text-decoration: none;
        transition: all var(--transition-1);
    }

    .search-form a:hover {
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: var(--smoky-black-2);
        border-radius: var(--radius-24);
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    th, td {
        padding: 15px 20px;
        text-align: left;
        border-bottom: 1px solid var(--eerie-black-2);
    }

    th {
        background-color: var(--smoky-black-3);
        color: var(--gold-crayola);
        font-weight: 700;
        text-transform: uppercase;
    }

    tr:hover {
        background-color: var(--smoky-black-3);
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .edit-btn, .delete-btn {
        padding: 8px 16px;
        border-radius: var(--radius-24);
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        transition: all var(--transition-1);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .edit-btn {
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
    }

    .delete-btn {
        background-color: #dc3545;
        color: var(--white);
    }

    .edit-btn:hover {
        background-color: var(--white);
        transform: translateY(-2px);
    }

    .delete-btn:hover {
        background-color: #bb2d3b;
        transform: translateY(-2px);
    }

    .material-symbols-outlined {
        font-size: 16px;
    }

    td[colspan="7"] {
        text-align: center;
        padding: 30px;
        color: var(--quick-silver);
        background-color: var(--smoky-black-3);
    }

     .admin-btn {
        display: inline-flex;
        padding: 12px 24px;
        background-color: var(--smoky-black-3);
        color: var(--gold-crayola);
        font-weight: 700;
        text-transform: uppercase;
        border-radius: var(--radius-24);
        text-decoration: none;
        transition: all var(--transition-1);
        border: 2px solid var(--gold-crayola);
        align-items: center;
        gap: 8px;
    }

    .admin-btn:hover {
        background-color: var(--gold-crayola);
        color: var(--smoky-black-1);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(228, 197, 144, 0.3);
    }
    </style>
</head>
<body>
    <h1>User Management</h1>
    
    <div class="nav-buttons" style="margin-bottom: 20px; text-align: center;">
        <a href="dashboard.php" class="admin-btn">
            <span class="material-symbols-outlined"></span>
            Back to Dashboard
        </a>
    </div>
    
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