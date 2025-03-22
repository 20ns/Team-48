<?php
// checkout.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); 

// 1) Ensure user is logged in
if (!isset($_SESSION['userID'])) {
    die("You need to be logged in to proceed to checkout.");
}

// 2) Connect to the database
$servername = "localhost";
$username   = "cs2team48";
$password   = "9ZReO56gOBkKTcr";
$dbname     = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3) Find the user’s shoppingSession ID
$userID       = $_SESSION['userID'];
$sessionQuery = "SELECT id FROM shoppingSession WHERE userID = ?";
$sessionStmt  = $conn->prepare($sessionQuery);
if (!$sessionStmt) {
    die("Prepare failed: " . $conn->error);
}
$sessionStmt->bind_param('i', $userID);
$sessionStmt->execute();
$sessionResult = $sessionStmt->get_result();

if ($sessionResult->num_rows === 0) {
    die("No active session found for this user.");
}
$sessionRow = $sessionResult->fetch_assoc();
$sessionID  = $sessionRow['id'];

// 4) Fetch cart items for this session
$query = "
    SELECT ci.id AS cartItemID, ci.quantity, p.name, p.price
    FROM cartItem ci
    JOIN product p ON ci.productID = p.id
    WHERE ci.sessionID = ?
";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param('i', $sessionID);
$stmt->execute();
$result = $stmt->get_result();

// Initialize a total
$total_cart = 0;

// Collect items in an array for easy display
$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $lineTotal = $row['price'] * $row['quantity'];
    $total_cart += $lineTotal;
    $cartItems[] = [
        'name'     => $row['name'],
        'price'    => $row['price'],
        'quantity' => $row['quantity'],
        'subtotal' => $lineTotal
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout - Peri Palace</title>

  <!-- Same stylesheet as your old checkout.html -->
  <link rel="stylesheet" href="./assets/css/style.css">

  <style>
    .checkout-section {
      padding: 60px 0;
      text-align: center;
    }
    .checkout-container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      background-color: var(--white-alpha-10);
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px hsla(0, 0%, 0%, 0.1);
      text-align: left;
    }
    .checkout-title {
      margin-bottom: 30px;
      color: var(--smoky-black);
      text-align: center;
      font-family: var(--ff-forum);
    }
    .order-summary {
      background: var(--black-alpha-15);
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 30px;
      text-align: center;
    }
    .order-summary h2 {
      font-family: var(--ff-forum);
      color: var(--white);
      margin-bottom: 10px;
    }
    .order-summary ul {
      list-style: none;
      padding: 0;
      margin: 0 0 10px 0;
      color: var(--white);
      font-family: var(--ff-dm-sans);
    }
    .order-summary .total {
      font-weight: bold;
      color: var(--gold-crayola);
      margin-top: 10px;
    }
    label {
      display: block;
      margin: 15px 0 5px;
      font-size: var(--fontSize-label-1);
      font-family: var(--ff-dm-sans);
      color: var(--white);
    }
    input[type="text"] {
      width: 100%;
      padding: 10px;
      background: var(--smoky-black-2);
      border: none;
      border-radius: 4px;
      color: var(--white);
      font-size: var(--fontSize-label-1);
      font-family: var(--ff-dm-sans);
    }
    .checkout-btn {
      width: 100%;
      padding: 12px;
      background: var(--gold-crayola);
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: var(--fontSize-label-2);
      color: var(--black);
      font-weight: var(--fw-700);
      margin-top: 20px;
      transition: background 0.3s;
    }
    .checkout-btn:hover {
      background: #c19a2f;
    }
    .not-logged-in {
      text-align: center;
      font-family: var(--ff-dm-sans);
      color: var(--white);
      margin-top: 20px;
    }
    .not-logged-in a {
      color: var(--gold-crayola);
      text-decoration: underline;
      font-weight: var(--fw-700);
    }
  </style>
</head>

<body id="top" class="loaded">

  <!-- Header (copied from your original checkout.html) -->
  <header class="header" data-header>
    <div class="container">
      <a href="index.php" class="logo">
        <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
      </a>

      <nav class="navbar" data-navbar>
        <button class="close-btn" aria-label="close menu" data-nav-toggler>
          <ion-icon name="close-outline" aria-hidden="true"></ion-icon>
        </button>
        <a href="index.php" class="logo">
          <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
        </a>
        <ul class="navbar-list">
          <li class="navbar-item">
            <a href="index.php" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">Home</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="index.php#menu" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">Menus</span>
            </a>
          </li>
          <li class="navbar-item">
            <a href="index.php#about" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">About Us</span>
            </a>
          </li>
        </ul>
      </nav>

      <a href="reservation.php" class="btn btn-secondary">
        <span class="text text-1">Book A Table</span>
        <span class="text text-2" aria-hidden="true">Book A Table</span>
      </a>

      <button class="nav-open-btn" aria-label="open menu" data-nav-toggler>
        <span class="line line-1"></span>
        <span class="line line-2"></span>
        <span class="line line-3"></span>
      </button>
    </div>
  </header>

  <main>
    <article>
      <section class="section checkout-section" aria-label="checkout">
        <div class="container checkout-container">
          <h1 class="checkout-title">Checkout</h1>

          <div class="order-summary">
            <h2>Your Order</h2>
            <ul id="orderList">
              <?php if (count($cartItems) === 0): ?>
                <li>Your cart is empty.</li>
              <?php else: ?>
                <?php foreach ($cartItems as $item): ?>
                  <li>
                    <?= htmlspecialchars($item['name']) ?>  
                    - £<?= number_format($item['price'], 2) ?> 
                    x <?= (int)$item['quantity'] ?> 
                    = £<?= number_format($item['subtotal'], 2) ?>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>

            <p class="total" id="totalAmount">
              <?php if (count($cartItems) > 0): ?>
                Total: £<?= number_format($total_cart, 2) ?>
              <?php else: ?>
                Total: £0.00
              <?php endif; ?>
            </p>
          </div>

          <!-- Billing/Payment Form (same as your old checkout.html) -->
          <!-- The form goes to paymentconfirmed.html -->
          <form id="checkoutForm" action="paymentconfirmed.html" method="POST">
            <h2 style="font-size: var(--fontSize-title-3); color: var(--white); margin-bottom:10px;">
              Billing & Shipping
            </h2>

            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="fullName" required>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" required>

            <label for="post">Postcode (Delivery operations are exclusive to Liverpool) </label>
            <input type="text" id="post" name="post" required>

            <h2 style="font-size: var(--fontSize-title-3); color: var(--white); margin-top:20px; margin-bottom:10px;">
              Payment Details
            </h2>

            <label for="cardNumber">Card Number</label>
            <input type="text" id="cardNumber" name="cardNumber"
                   placeholder="XXXX XXXX XXXX XXXX" maxlength="19" required>

            <label for="expiry">Expiry (MM/YY)</label>
            <input type="text" id="expiry" name="expiry"
                   placeholder="MM/YY" required>

            <label for="cvc">CVC</label>
            <input type="text" id="cvc" name="cvc"
                   placeholder="XXX" maxlength="3" required>

            <button type="submit" class="checkout-btn">Place Order</button>
          </form>

        </div>
      </section>
    </article>
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

  <!-- Ionicons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

 
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const checkoutForm    = document.getElementById('checkoutForm');
      const cardNumberInput = document.getElementById('cardNumber');
      const expiryInput     = document.getElementById('expiry');
      const cvcInput        = document.getElementById('cvc');

      const applyPromoBtn   = document.getElementById('applyPromoBtn');
      const promoCodeInput  = document.getElementById('promoCode');
      const promoMessage    = document.getElementById('promoMessage');
      const totalAmountEl   = document.getElementById('totalAmount');

      // ==========================================
      // AUTO-FORMAT CARD NUMBER & EXPIRY
      // ==========================================
      cardNumberInput.addEventListener('input', function(e) {
        let input = e.target.value.replace(/\D/g, '');
        if (input.length > 16) input = input.slice(0, 16);
        e.target.value = input.match(/.{1,4}/g)?.join(' ') || input;
      });
      expiryInput.addEventListener('input', function(e) {
        let input = e.target.value.replace(/[^\d]/g, '');
        if (input.length > 2) {
          input = input.substring(0, 2) + '/' + input.substring(2, 4);
        }
        e.target.value = input;
      });

      // ==========================================
      // VALIDATION (Full Name, Address, Postcode, etc.)
      // ==========================================
      checkoutForm.addEventListener('submit', function(event) {
        if (!validateCheckoutForm()) {
          event.preventDefault();
        }
      });

      function validateCheckoutForm() {
        const fullName  = document.getElementById('fullName').value.trim();
        const address   = document.getElementById('address').value.trim();
        const post      = document.getElementById('post').value.trim();
        const cardRaw   = cardNumberInput.value.trim();
        const cardNum   = cardRaw.replace(/\s+/g, '');
        const expiry    = expiryInput.value.trim();
        const cvc       = cvcInput.value.trim();

        // Full Name: 2+ letters only
        if (!/^[A-Za-z\s]{2,50}$/.test(fullName)) {
          alert('Full Name must be at least 2 letters (only letters & spaces).');
          return false;
        }
        // Address: at least 5 chars
        if (address.length < 5) {
          alert('Address must be at least 5 characters.');
          return false;
        }
        // Postcode: must match "Lxx xxx"
        if (!/^L[0-9]{1,2}\s?[0-9][A-Za-z]{2}$/.test(post)) {
          alert('Enter a valid Liverpool postcode (e.g. L1 1AA, L12 3AB).');
          return false;
        }
        // Card number: exactly 16 digits
        if (!/^\d{16}$/.test(cardNum)) {
          alert('Enter a valid 16-digit card number.');
          return false;
        }
        // Expiry: MM/YY
        if (!/^\d{2}\/\d{2}$/.test(expiry)) {
          alert('Expiry must be in MM/YY format.');
          return false;
        } else {
          const [mm, yy]     = expiry.split('/');
          const expMonth     = parseInt(mm, 10);
          const expYear      = parseInt(yy, 10);
          const currentYear  = new Date().getFullYear() % 100;
          const currentMonth = new Date().getMonth() + 1;
          if (expMonth < 1 || expMonth > 12) {
            alert('Expiry month must be between 01 and 12.');
            return false;
          }
          if (expYear < currentYear || (expYear === currentYear && expMonth < currentMonth)) {
            alert('This card appears to be expired.');
            return false;
          }
        }
        // CVC: exactly 3 digits
        if (!/^\d{3}$/.test(cvc)) {
          alert('Enter a valid 3-digit CVC.');
          return false;
        }
        return true;
      }

      // ==========================================
      // PROMO CODES
      // ==========================================
      applyPromoBtn.addEventListener('click', function() {
        const code = promoCodeInput.value.trim().toUpperCase();
        if (!code) {
          promoMessage.style.color = "red";
          promoMessage.textContent = "Please enter a promo code.";
          return;
        }

       
        const match = totalAmountEl.textContent.match(/([\d\.]+)/);
        if (!match) return;
        let currentTotal = parseFloat(match[1]); 

        
        let discountRate = 0;
        const now = Date.now();
        const spicyExpiry = new Date("2025-04-21").getTime();

        if (code === "SPICY10") {
          
          if (now > spicyExpiry) {
            promoMessage.style.color = "red";
            promoMessage.textContent = "SPICY10 is expired.";
            return;
          } else {
            discountRate = 0.10; 
            promoMessage.style.color = "var(--gold-crayola)";
            promoMessage.textContent = "SPICY10 applied! 10% discount.";
          }
        } else if (code === "PERI25") {
          discountRate = 0.08;
          promoMessage.style.color = "var(--gold-crayola)";
          promoMessage.textContent = "PERI25 applied! 8% discount.";
        } else {
          promoMessage.style.color = "red";
          promoMessage.textContent = "Invalid promo code.";
          return;
        }

        // Recalculate total
        const newTotal = (currentTotal * (1 - discountRate)).toFixed(2);
        totalAmountEl.textContent = "Total: £" + newTotal;
      });

    });
  </script>

</body>
</html>

