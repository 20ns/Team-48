<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../connection.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $category = $_POST['category'] ?? '';
    $image = '';

    // Validate inputs
    if (empty($name) || empty($description) || empty($category)) {
        $error = 'Please fill in all required fields';
    } elseif (!is_numeric($price) || !is_numeric($stock)) {
        $error = 'Price and Stock must be numbers';
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "./assets/images/";
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetFile = $targetDir . $fileName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if image file is valid
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $image = $fileName;
                } else {
                    $error = 'Error uploading image';
                }
            } else {
                $error = 'Invalid image format. Allowed: JPG, JPEG, PNG, GIF';
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO product 
                    (image, name, description, stock, category, price) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $image,
                    $name,
                    $description,
                    $stock,
                    $category,
                    $price
                ]);

                $success = 'Product added successfully!';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <!-- Include your existing stylesheets -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
    .product-form {
        max-width: 800px;
        margin: 50px auto;
        padding: 40px;
        background-color: var(--smoky-black-2);
        border-radius: var(--radius-24);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: var(--gold-crayola);
    }

    input, textarea, select {
        width: 100%;
        padding: 12px;
        background-color: var(--smoky-black-3);
        border: 1px solid var(--gold-crayola);
        border-radius: 8px;
        color: var(--white);
        font-size: 16px;
    }

    input[type="file"] {
        padding: 6px;
    }

    .message {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
    }

    .error {
        background-color: #ff4444;
        color: white;
    }

    .success {
        background-color: #00C851;
        color: white;
    }
    </style>
</head>
<body>
    <section class="admin-section">
        <div class="dashboard-container">
            <h1>Add New Product</h1>
            
            <?php if ($error): ?>
                <div class="message error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="message success"><?= $success ?></div>
            <?php endif; ?>

            <form class="product-form" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="starter">Starter</option>
                        <option value="main">Main Course</option>
                        <option value="sides">Sides</option>
                        <option value="dessert">Dessert</option>
                        <option value="drink">Drink</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>

                <button type="submit" class="button">
                    <span class="material-symbols-outlined">add</span>
                    Add Product
                </button>
            </form>

            <div style="margin-top: 30px;">
                <a href="admin_dashboard.php" class="button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </section>
</body>
</html>