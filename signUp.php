<?php
$servername = "localhost";
$username = "cs2team48";
$password = "your_database_password";
$dbname = "cs2team48_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $hashed_password = password_hash($password, PASSWORD_BCRYPT);


    $sql = "INSERT INTO users (email, password) VALUES (?, ?)";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Signup successful! <a href='signup.html'>Back to signup</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
