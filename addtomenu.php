<?php
$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $imagePath = ''; 
  
  // checking if image uploaded
  if (isset($_FILES['itemImage']) && $_FILES['itemImage']['error'] == 0) {
    $imageName = $_FILES['itemImage']['name'];
    $imageTmpName = $_FILES['itemImage']['tmp_name'];
    $imageSize = $_FILES['itemImage']['size'];
    $imageError = $_FILES['itemImage']['error'];

    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $imageType = mime_content_type($imageTmpName);

    
    if (in_array($imageType, $allowedTypes)) {
      
      if ($imageSize < 5 * 1024 * 1024) {
        
        $uniqueName = uniqid('', true) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
        $targetDir = './assets/images/';
        $targetFile = $targetDir . $uniqueName;

        if (move_uploaded_file($imageTmpName, $targetFile)) {
          $imagePath = $targetFile; 
        } else {
          echo "Error uploading the image.";
        }
      } else {
        echo "Image file is too large. Maximum size is 5MB.";
      }
    } else {
      echo "Only JPEG, PNG, and GIF images are allowed.";
    }
  }

  
  $name = $_POST["itemName"];
  $price = $_POST["itemPrice"];
  $description = $_POST["itemDescription"];
  $category = $_POST["itemCategory"];

  // if an image was uploaded, save the file path to the database
  if (!empty($imagePath)) {
    
    $stmt = mysqli_prepare($conn, "INSERT INTO product(id, image, name, description, stock, category, price) VALUES (NULL, ?, ?, ?, 100, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssssd', $imagePath, $name, $description, $category ,$price);

    if (mysqli_stmt_execute($stmt)) {
      echo "Item added to menu successfully.";
      header("Location: index.html#menu");
      exit();
    } else {
      echo "Error: " . mysqli_error($conn);
    }
  } else {
    echo "No image was uploaded.";
  }
}
?>
