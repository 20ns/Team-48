<?php
$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST["removefrommenu"])) {
    $name = trim($_POST["name"]);  // Trim any extra spaces

    // Sanitize name before proceeding
    if (empty($name)) {
        echo "Error: Item name is empty!";
        exit();
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM product WHERE name = ?");
    if ($stmt === false) {
        die('MySQL prepare failed: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 's', $name);
    
    if (mysqli_stmt_execute($stmt)) {
        // Check if any row was deleted
        if (mysqli_affected_rows($conn) > 0) {
            echo "Item removed successfully.";
        } else {
            echo "No item found with that name to delete.";
        }
        
        // Get the URL from the POST data
        $url = $_POST["url"];
        
        // Correctly concatenate the URL for the header redirect
        header("Location: " . $url);
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>