<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database credentials
$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";

// Create a new MySQL connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Will print error message if connection fails
}

// Check if the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['name']) && !empty($_POST['phone']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        // Collect form data
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if the email already exists in the database
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email already exists
            echo "Email already in use. Please try a different email.";
        } else {
            // Insert the new user data into the database
            $sql = "INSERT INTO users (name, phone_number, email, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("ssss", $name, $phone, $email, $hashed_password);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Signup successful! <a href='logIn.html'>Go to Login</a>";
            } else {
                echo "Error k : " . $stmt->error;
            }
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Please fill in all fields.";
    }
}

// Close the connection
$conn->close();
?>
