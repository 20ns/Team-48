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
