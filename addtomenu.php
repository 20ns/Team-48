<?php
$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST["addtomenu"])) {
  $image = $_POST["itemImage"];
  $name = $_POST["itemName"];
  $price = $_POST["itemPrice"];
  $description = $_POST["itemDescription"];
  $category = $_POST["itemCategory"];

  $stmt = mysqli_prepare($conn, "INSERT INTO product(id, image, name, description, stock, category, price) VALUES (NULL, ?, ?, ?, 100, ?, ?)");
  mysqli_stmt_bind_param($stmt, 'ssssd', $image, $name, $description, $category ,$price);


  if (mysqli_stmt_execute($stmt)) {
    echo "Item added to menu successfully.";
    
    header("Location: index.html#menu");
    exit();
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}
?>
