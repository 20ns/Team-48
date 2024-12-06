<?php
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

  $stmt = mysqli_prepare($conn, "INSERT INTO items(id, name, quantity, price) VALUES (?, ?, ?, ?)");
  mysqli_stmt_bind_param($stmt, 'isdi', $id, $name, $quantity, $price);


  if (mysqli_stmt_execute($stmt)) {
    echo "Item added to cart successfully.";
    
    header("Location: basket.html");
    exit();
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}
?>
