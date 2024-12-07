<?php
$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if an item needs to be removed
if (isset($_GET['remove_item_id'])) {
  $item_id = $_GET['remove_item_id'];

  // SQL query to delete the item
  $delete_sql = "DELETE FROM items WHERE id = $item_id";
  if ($conn->query($delete_sql) === TRUE) {
    echo "Item removed successfully";
  } else {
    echo "Error: " . $conn->error;
  }
}

// Check if an item quantity needs to be updated
if (isset($_POST['update_item_id']) && isset($_POST['new_quantity'])) {
  $item_id = $_POST['update_item_id'];
  $new_quantity = $_POST['new_quantity'];

  // SQL query to update the item quantity
  $update_sql = "UPDATE items SET quantity = $new_quantity WHERE id = $item_id";
  if ($conn->query($update_sql) === TRUE) {
    echo "Item quantity updated successfully";
  } else {
    echo "Error: " . $conn->error;
  }
}

$sql = "SELECT id, name, price, quantity FROM items";
$result = $conn->query($sql);
$total_cart = 0; // Variable to store the total cart value
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

  <!-- <link rel="stylesheet" href="./assets/css/style.css"> -->

  <style>
    body {
      font-family: 'DM Sans', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
    }

    .basket-section {
      padding: 20px;
    }

    .basket-table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      font-size: 1rem;
      text-align: left;
      background-color: #fff;
    }

    .basket-table th,
    .basket-table td {
      padding: 15px;
      border: 1px solid #ddd;
      text-align: center;
    }

    .basket-table th {
      background-color: #343a40;
      color: white;
      font-weight: bold;
    }

    .basket-table td:first-child {
      text-align: left;
    }

    .btn-small {
      display: inline-block;
      padding: 5px 10px;
      background-color: #dc3545;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-size: 0.9rem;
    }

    .btn-small:hover {
      background-color: #c82333;
    }

    .update-btn {
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 5px 10px;
      cursor: pointer;
      font-size: 0.9rem;
    }

    .update-btn:hover {
      background-color: #0056b3;
    }

    .basket-total {
      text-align: right;
      font-size: 1.2rem;
      margin-top: 20px;
    }

    .return-btn-container {
      margin-top: 30px;
      text-align: center;
    }

    .btn-primary {
      display: inline-block;
      background-color: #28a745;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 5px;
      font-size: 1rem;
    }

    .btn-primary:hover {
      background-color: #218838;
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
          <?php
          if ($result->num_rows > 0) {
            echo '<table class="basket-table">';
            echo '<thead><tr><th>Item</th><th>Price</th><th>Quantity</th><th>Total</th><th>Action</th><th>Update Quantity</th></tr></thead><tbody>';
            while($row = $result->fetch_assoc()) {
              $total = $row['price'] * $row['quantity'];
              $total_cart += $total; // Add to cart total

              $formatted_total = "£" . number_format($total, 2); // Format total as currency with two decimal places

              echo "<tr>
                      <td>" . $row["name"] . "</td>
                      <td>£" . number_format($row["price"], 2) . "</td> <!-- Format price with pound sign -->
                      <td>" . $row["quantity"] . "</td>
                      <td>" . $formatted_total . "</td> <!-- Display formatted total -->
                      <td><a href='?remove_item_id=" . $row["id"] . "' class='remove-btn'>Remove</a></td>
                      <td>
                        <form action='' method='POST'>
                          <input type='number' name='new_quantity' value='" . $row["quantity"] . "' min='1' required>
                          <input type='hidden' name='update_item_id' value='" . $row["id"] . "'>
                          <button type='submit' class='update-btn'>Update</button>
                        </form>
                      </td>
                    </tr>";
            }
            echo '</tbody></table>';

            // Display the total cart amount
            echo '<p class="total-cart">Total: £' . number_format($total_cart, 2) . '</p>';
          } else {
            echo "<p>Your basket is empty.</p>";
          }

          $conn->close();
          ?>
          
          
          <a href="index.html#menu" class="btn btn-primary">
            <span class="text text-1">Return to Menu</span>
            <span class="text text-2" aria-hidden="true">Return to Menu</span>
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