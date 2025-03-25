<?php
session_start();
$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, image, name, description, stock, category, price FROM product WHERE category = 'main'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap" rel="stylesheet">
  <title>Peri Palace - Mains Menu</title>
  <style>
    /* Inline styling for specific adjustments */
    .grid-list {
      display: grid;
      gap: 2rem;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      justify-items: center;
    }

    .main-card {
      background-color: var(--gray-100);
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      width: 100%;
      max-width: 350px;
    }

    .main-card:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card-banner {
      --width: 320px;
      --height: 200px;
      margin-bottom: 1rem;
    }

    .img-cover {
      border-radius: 10px 10px 0 0;
      width: 100%;
      height: auto;
    }

    .card-content {
      padding: 1rem;
      text-align: left;
    }

    .card-title {
      color: var(--yellow);
      margin-bottom: 0.5rem;
    }

    .price {
      display: block;
      margin-top: 0.5rem;
      font-size: 1.2rem;
      color: var(--primary-color);
    }

    .label-1 {
      font-size: 1rem;
      color: var(--text-color);
    }
    
    /* Optional: Styles for stock status if needed */
    .stock-status {
      display: inline-block;
      margin-top: 0.5rem;
      font-size: 0.9rem;
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
    }
    .in-stock { background: #d4edda; color: #155724; }
    .low-stock { background: #fff3cd; color: #856404; }
    .out-of-stock { background: #f8d7da; color: #721c24; }
  </style>
</head>

<body id="top" class="mains-page">

  <!-- HEADER SECTION -->
  <header class="header" data-header>
    <div class="container">
      <a href="./index.html" class="logo">
        <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace - Home">
      </a>
      <nav class="navbar" data-navbar>
        <ul class="navbar-list">
          <li class="navbar-item"><a href="./index.html#home" class="navbar-link hover-underline">Home</a></li>
          <li class="navbar-item"><a href="./index.html#menu" class="navbar-link hover-underline">Menus</a></li>
          <li class="navbar-item"><a href="./index.html#about" class="navbar-link hover-underline">About Us</a></li>
          <li class="navbar-item"><a href="./index.html#contact" class="navbar-link hover-underline">Contact</a></li>
          <li class="navbar-item"><a href="basket.php" class="navbar-link hover-underline">Basket</a></li>
        </ul>
      </nav>
      <button class="nav-open-btn" aria-label="open menu" data-nav-toggler>
        <span class="line line-1"></span>
        <span class="line line-2"></span>
        <span class="line line-3"></span>
      </button>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <!-- NOTE: Stock decrement will be handled when user functionalities are complete -->
  <main>
    <section class="section mains text-center" id="mains">
      <div class="container">
        <div class="search-container">
          <?php include 'filters.php'; ?>
          <?php renderFilters(); ?>
          <input id="searchInput" type="text" placeholder="Search for mains..." class="search-input">
        </div>
        <h2 class="headline-1 section-title">Our Mains Selection</h2>
        <p class="section-subtitle label-2">
          Indulge in our exquisite main course meals - prepared to perfection, with bold flavors and a touch of elegance to satisfy your palate.
        </p>

        <?php
          if ($result->num_rows > 0) {
            echo "<ul class='grid-list mains-list'>";
            while ($row = $result->fetch_assoc()) {
              $stock = $row["stock"];
              if ($stock == 0) {
                $stockMessage = "<span class='stock-status out-of-stock'>Out of Stock</span>";
              } elseif ($stock < 10) {
                $stockMessage = "<span class='stock-status low-stock'>Low Stock</span>";
              } else {
                $stockMessage = "<span class='stock-status in-stock'>In Stock</span>";
              }
              echo '<li class="main-item">
                <div class="main-card">
                  <figure class="card-banner img-holder">
                    <img src="' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["name"]) . '" class="img-cover">
                  </figure>
                  <div class="card-content">
                    <h3 class="title-4 card-title">' . htmlspecialchars($row["name"]) . '</h3>
                    <p class="card-description label-1">' . htmlspecialchars($row["description"]) . '</p>
                    <span class="price">£' . number_format($row["price"], 2) . '</span>
                    ' . $stockMessage . '
                    <div class="btn-group">
                      <form method="POST" action="addtocart.php">
                        <input type="hidden" name="id" value="' . $row["id"] . '">
                        <input type="hidden" name="name" value="' . htmlspecialchars($row["name"]) . '">
                        <input type="hidden" name="price" value="' . $row["price"] . '">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="image" value="' . htmlspecialchars($row["image"]) . '">
                        <input type="hidden" name="url" value="mains.php">
                        <button class="btn btn-primary" type="submit" name="addtocart">Add to Cart</button>
                      </form>
                      <h3 class="title-4 card-title">' . $stockMessage . '</h3>
                    </div>
                  </div>
                </div>
              </li>';
            }
            echo "</ul>";
          } else {
            echo "<p>No items found.</p>";
          }
          $conn->close();
        ?>
      </div>
    </section>
  </main>

  <!-- FOOTER SECTION -->
  <footer class="footer section has-bg-image text-center" style="background-image: url('./assets/images/newBackSpice.jpg')">
    <div class="container">
      <div class="footer-top grid-list">
        <div class="footer-brand has-before has-after">
          <a href="./index.html" class="logo">
            <img src="./assets/images/logoWhite.png" width="160" height="50" alt="Peri Palace home">
          </a>
          <address class="body-4">Corporate Street, Stratford Rd, Liverpool 8976, UK</address>
          <a href="mailto:Peri-booking@palace.com" class="body-4 contact-link">Peri-booking@palace.com</a>
          <a href="tel:+29056745321" class="body-4 contact-link">Booking Request: +2 905 674 5321</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- BACK TO THE TOP -->
  <a href="#top" class="back-top-btn active" aria-label="back to top" data-back-top-btn>
    <ion-icon name="chevron-up" aria-hidden="true"></ion-icon>
  </a>

  <!-- Custom JS -->
  <script src="./assets/js/script.js"></script>
  <script src="./assets/js/search-bar.js"></script>
  <script src="./filters.js"></script>

  <!-- Ionicon Link -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
