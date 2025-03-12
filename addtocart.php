<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['userID'])) {
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Required</title>
        <link rel="stylesheet" href="./assets/css/style.css">
        <style>
            .login-message-container {
                width: 100%;
                max-width: 400px;
                margin: 10% auto;
                background-color: var(--black-alpha-15);
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 4px 10px hsla(0, 0%, 0%, 0.2);
                text-align: center;
            }
            .login-message {
                color: var(--white);
                font-family: var(--fontFamily-dm_sans);
                font-size: var(--fontSize-body-2);
                font-weight: var(--weight-bold);
                margin-bottom: 15px;
            }
            .button-container {
                display: flex;
                justify-content: center;
                gap: 10px;
            }
            .action-button {
                display: inline-block;
                padding: 10px 20px;
                background: var(--gold-crayola);
                color: var(--black);
                border-radius: 5px;
                font-size: var(--fontSize-label-2);
                font-weight: var(--weight-bold);
                text-decoration: none;
                transition: background 0.3s;
            }
            .action-button:hover {
                background: #c19a2f;
            }
        </style>
    </head>
    <body id="top" class="loaded">

        <!-- Header -->
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
                        <li class="navbar-item"><a href="index.html" class="navbar-link hover-underline"><div class="separator"></div><span class="span">Home</span></a></li>
                        <li class="navbar-item"><a href="index.html#menu" class="navbar-link hover-underline"><div class="separator"></div><span class="span">Menus</span></a></li>
                        <li class="navbar-item"><a href="index.html#about" class="navbar-link hover-underline"><div class="separator"></div><span class="span">About Us</span></a></li>
                    </ul>
                </nav>
                <a href="#" class="btn btn-secondary">
                    <span class="text text-1">Book A Table</span>
                    <span class="text text-2" aria-hidden="true">Book A Table</span>
                </a>
                <button class="nav-open-btn" aria-label="open menu" data-nav-toggler>
                    <span class="line line-1"></span><span class="line line-2"></span><span class="line line-3"></span>
                </button>
            </div>
        </header>

        <!-- Login Required Message -->
        <div class="login-message-container">
            <p class="login-message">You must be logged in to add items to the cart.</p>
            <div class="button-container">
                <a href="logIn.php" class="action-button">Login Here</a>
                <a href="javascript:history.back()" class="action-button">Go Back</a>
            </div>
        </div>

    </body>
    </html>
    ';
    exit();
}

if(isset($_POST["addtocart"])) {
  $id = $_POST["id"];
  $name = $_POST["name"];
  $price = $_POST["price"];
  $quantity = $_POST["quantity"];
  $user_id = $_SESSION['user_id'];

  $stmt = mysqli_prepare($conn, "INSERT INTO items(id, name, quantity, price, user_id) VALUES (?, ?, ?, ?, ?)");
  mysqli_stmt_bind_param($stmt, 'isdii', $id, $name, $quantity, $price, $_SESSION['user_id']);


  if (mysqli_stmt_execute($stmt)) {
    echo "Item added to cart successfully.";
    // Right now I just made the add to cart redirect you to the basket after you add something.
    // In the future if you want to implement it going back to the right page, each category of item starts with a different number on the id
    // Main = 1
    // Starters = 2
    // Drinks = 3
    // Sides = 4
    // Desserts = 5
    header("Location: basket.php");
    exit();
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}
?>
