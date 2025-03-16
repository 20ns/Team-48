<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'connection.php';

$user_id = $_SESSION['user_id'];

// Remove item
if (isset($_GET['remove_item_id'])) {
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $_GET['remove_item_id'], $user_id);
    $stmt->execute();
    $stmt->close();
}

// Update quantity
if (isset($_POST['update_item_id']) && isset($_POST['new_quantity'])) {
    $stmt = $conn->prepare("UPDATE items SET quantity = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $_POST['new_quantity'], $_POST['update_item_id'], $user_id);
    $stmt->execute();
    $stmt->close();
}

// Get items (mysqlnd compatible version)
$stmt = $conn->prepare("SELECT id, name, price, quantity FROM items WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $name, $price, $quantity);

$total_cart = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Basket - Peri Palace</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=shopping_basket">

  <link rel="stylesheet" href="./assets/css/style.css">

  <style>
.update-btn {
  background-color: var(--gold-crayola); 
  color: var(--smoky-black-1); 
  border: none;
  border-radius: var(--radius-24); 
  padding: 8px 16px; 
  cursor: pointer;
  font-size: 1rem;
  font-weight: var(--weight-bold);
  transition: background-color var(--transition-1), color var(--transition-1), transform var(--transition-1); /* Smooth transitions */
}

.update-btn:hover {
  background-color: var(--smoky-black-2);
  color: var(--white); 
  transform: scale(1.05); 
}
.basket-section {
  padding: 20px;
  background-color: var(--eerie-black-1);
  color: var(--white);
}

.basket-table {
  width: 100%;
  border-collapse: collapse;
  margin: 30px 0;
  font-size: 1rem;
  text-align: left;
  background-color: var(--smoky-black-2);
  box-shadow: 0 2px 5px var(--black-alpha-15);
  border-radius: var(--radius-24);
}

.basket-table th {
  background-color: var(--smoky-black-3);
  color: var(--gold-crayola); 
  font-weight: var(--weight-bold);
  padding: 18px 25px;
  text-align: center;
}

.basket-table td {
  padding: 18px 25px;
  border: 1px solid var(--eerie-black-2);
  text-align: center;
  color: var(--quick-silver); 
}

.basket-table td:first-child {
  text-align: left;
  color: var(--white); 
}

.basket-table input[type="number"] {
  width: 70px;
  margin-right: 10px;
  margin-bottom: 10px;
  padding: 6px;
  border: 1px solid var(--quick-silver); 
  border-radius: var(--radius-24);
  text-align: center;
  background-color: var(--eerie-black-2);
  color: var(--white);
}

  </style>
</head>

<body id="top" class="loaded">

  <header class="header" data-header>
    <div class="container">
      <a href="index.html" class="logo">
        <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
      </a>

      <nav class="navbar" data-navbar>
        <button class="close-btn" aria-label="close menu" data-nav-toggler>
          <ion-icon name="close-outline" aria-hidden="true"></ion-icon>
        </button>

        <a href="index.html" class="logo">
          <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
        </a>

        <ul class="navbar-list">
          <li class="navbar-item">
            <a href="index.html" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">Home</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="index.html#menu" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">Menus</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="index.html#about" class="navbar-link hover-underline">
              <div class="separator"></div>
              <span class="span">About Us</span>
            </a>
          </li>
        </ul>
      </nav>

      <a href="#" class="btn btn-secondary">
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
      <section class="section basket-section" aria-label="basket">
        <div class="container">
          <h1 class="headline-1 section-title">Your Basket</h1>

          <div class="basket-items">
          <?php
            if ($stmt->num_rows > 0) {
                echo '<table class="basket-table">';
                echo '<thead><tr><th>Item</th><th>Price</th><th>Quantity</th><th>Total</th><th>Action</th><th>Update Quantity</th></tr></thead><tbody>';
                
                while ($stmt->fetch()) {
                    $total = $price * $quantity;
                    $total_cart += $total;
                    echo "<tr class='basket-item'>
                            <td class='item-name'>" . htmlspecialchars($name) . "</td>
                            <td class='item-price'>£" . number_format($price, 2) . "</td>
                            <td>" . $quantity . "</td>
                            <td>£" . number_format($total, 2) . "</td>
                            <td>
                                <a href='?remove_item_id=" . $id . "' class='remove-btn btn-danger'>Remove</a>
                            </td>
                            <td>
                                <form action='' method='POST'>
                                    <input type='number' name='new_quantity' value='" . $quantity . "' min='1' required>
                                    <input type='hidden' name='update_item_id' value='" . $id . "'>
                                    <button type='submit' class='update-btn'>Update</button>
                                </form>
                            </td>
                          </tr>";
                }
                
                echo '</tbody></table>';
                echo '<p class="total">Total: £' . number_format($total_cart, 2) . '</p>';
            } else {
                echo "<p>Your basket is empty.</p>";
            }
            $stmt->close();
            $conn->close();
          ?>
</div>

          <a href="index.html#menu" class="btn btn-primary">
            <span class="text text-1">Return to Menu</span>
            <span class="text text-2" aria-hidden="true">Return to Menu</span>
          </a>
          <a href="checkout.html" class="btn btn-primary">
            <span class="text text-1">Proceed to checkout</span>
            <span class="text text-2" aria-hidden="true">Proceed to checkout</span>
          </a>
        </div>
      </section>
    </article>
  </main>

 


  <!-- Footer -->
  <footer class="footer section has-bg-image text-center">
    <div class="container">
      <div class="footer-bottom">
        <p class="copyright">
          &copy; 2024 Peri Palace. All Rights Reserved
        </p>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="script.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>
</html>
