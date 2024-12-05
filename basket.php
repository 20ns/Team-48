<?php
$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, price, quantity FROM items";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "Item: " . $row["Name"]. " - Price: " . $row["price"]. " - Quantity " . $row["Quantity"]. "<br>";
  }
} else {
  echo "Basket Empty";
}
$conn->close();
?>