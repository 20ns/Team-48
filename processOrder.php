<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['userID'])) {
    die("You must be logged in to place an order.");
}

$servername = "localhost";
$username   = "cs2team48";
$password   = "9ZReO56gOBkKTcr";
$dbname     = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID   = $_SESSION['userID'];
$fullName = $_POST['fullName'] ?? '';
$address  = $_POST['address'] ?? '';
$postcode = $_POST['post'] ?? '';
$total    = 0;

$conn->begin_transaction();

try {
   
    $stmt = $conn->prepare("SELECT id FROM shoppingSession WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $sessionResult = $stmt->get_result();
    if ($sessionResult->num_rows === 0) {
        throw new Exception("No active shopping session found.");
    }
    $sessionID = $sessionResult->fetch_assoc()['id'];

    
    $stmt = $conn->prepare("
        SELECT ci.productID, ci.quantity, p.price
        FROM cartItem ci
        JOIN product p ON ci.productID = p.id
        WHERE ci.sessionID = ?
    ");
    $stmt->bind_param("i", $sessionID);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItems = [];
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
        $total += $row['price'] * $row['quantity'];
    }

    if (empty($cartItems)) {
        throw new Exception("Cart is empty.");
    }

   
    $stmt = $conn->prepare("INSERT INTO orderDetails (userID, total, status, created_at) VALUES (?, ?, 'pending', NOW())");
    $stmt->bind_param("id", $userID, $total);
    $stmt->execute();
    $orderID = $stmt->insert_id;

    
    $stmt = $conn->prepare("INSERT INTO orderItems (orderID, productID, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmt->bind_param("iiid", $orderID, $item['productID'], $item['quantity'], $item['price']);
        $stmt->execute();

        
        $stockUpdate = $conn->prepare("UPDATE product SET stock = stock - ? WHERE id = ?");
        $stockUpdate->bind_param("ii", $item['quantity'], $item['productID']);
        $stockUpdate->execute();
    }

   
    $clearStmt = $conn->prepare("DELETE FROM cartItem WHERE sessionID = ?");
    $clearStmt->bind_param("i", $sessionID);
    $clearStmt->execute();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    die("Order processing failed: " . $e->getMessage());
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Confirmed - Peri Palace</title>
  <link rel="stylesheet" href="./assets/css/style.css">
  <style>
    .confirmation-wrapper {
      padding: 60px 20px;
      text-align: center;
    }
    .confirmation-box {
      max-width: 600px;
      margin: 0 auto;
      background-color: var(--white-alpha-10);
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px hsla(0, 0%, 0%, 0.1);
    }
    .confirmation-box h1 {
      color: var(--gold-crayola);
      margin-bottom: 20px;
      font-family: var(--ff-forum);
    }
    .confirmation-box p {
      color: var(--white);
      font-size: 1.1rem;
      font-family: var(--ff-dm-sans);
    }
    .confirmation-box a {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 20px;
      background: var(--gold-crayola);
      color: var(--black);
      font-weight: bold;
      text-decoration: none;
      border-radius: 6px;
      transition: background 0.3s;
    }
    .confirmation-box a:hover {
      background: #c19a2f;
    }
  </style>
</head>
<body class="loaded">

  <header class="header" data-header>
    <div class="container">
      <a href="index.php" class="logo">
        <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
      </a>
    </div>
  </header>

  <main>
    <section class="confirmation-wrapper">
      <div class="confirmation-box">
        <h1>Thank You for Your Order!</h1>
        <p>Your order has been placed successfully. We are preparing your delicious meal!</p>
        <p>Order ID: <strong>#<?= htmlspecialchars($orderID) ?></strong></p>
        <a href="my_orders.php">View My Orders</a>
      </div>
    </section>
  </main>

  <footer class="footer section has-bg-image text-center">
    <div class="container">
      <div class="footer-bottom">
        <p class="copyright">
          &copy; 2024 Peri Palace. All Rights Reserved
        </p>
      </div>
    </div>
  </footer>

</body>
</html>
