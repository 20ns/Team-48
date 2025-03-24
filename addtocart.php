<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: logIn.php");
    exit();
}

require_once 'connection.php';

if(isset($_POST["addtocart"])) {
    $productID = $_POST["id"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $userID = $_SESSION['userID'];

    try {
        // Check if product exists and has stock
        $stmt = $pdo->prepare("SELECT stock FROM product WHERE id = ?");
        $stmt->execute([$productID]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product || $product["stock"] < 1) {
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Item Unavailable</title>
                <link rel="stylesheet" href="./assets/css/style.css">
                <style>
                    .popup-overlay {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 1000;
                    }
                    .popup-container {
                        background-color: var(--black-alpha-15);
                        padding: 20px;
                        border-radius: 12px;
                        text-align: center;
                        max-width: 400px;
                        box-shadow: 0 4px 10px hsla(0, 0%, 0%, 0.2);
                    }
                </style>
            </head>
            <body>
                <div class="popup-overlay">
                    <div class="popup-container">
                        <p class="popup-message">Sorry, this item is currently out of stock.</p>
                        <a href="javascript:history.back()" class="popup-button">Go Back</a>
                    </div>
                </div>
            </body>
            </html>';
            exit();
        }

        // Get or create shopping session
        $sessionStmt = $pdo->prepare("SELECT id FROM shoppingSession WHERE userID = ?");
        $sessionStmt->execute([$userID]);
        $session = $sessionStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session) {
            $sessionStmt = $pdo->prepare("INSERT INTO shoppingSession (userID, total, created_at, modified_at) VALUES (?, 0, NOW(), NOW())");
            $sessionStmt->execute([$userID]);
            $sessionID = $pdo->lastInsertId();
        } else {
            $sessionID = $session['id'];
        }

        // Add item to cart
        $cartStmt = $pdo->prepare("INSERT INTO cartItem (sessionID, productID, quantity) VALUES (?, ?, ?)");
        $cartStmt->execute([$sessionID, $productID, $quantity]);

        // Update product stock
        $updateStmt = $pdo->prepare("UPDATE product SET stock = stock - ? WHERE id = ?");
        $updateStmt->execute([$quantity, $productID]);

        header("Location: basket.php");
        exit();

    } catch(PDOException $e) {
        error_log("Error adding to cart: " . $e->getMessage());
        echo "Error adding item to cart. Please try again.";
        exit();
    }
}

header("Location: index.php");
exit();
?>