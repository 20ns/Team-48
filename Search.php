<?php
$servername = "localhost";
$username = "cs2team48";
$password = "9ZReO56gOBkKTcr";
$dbname = "cs2team48_db";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET['meal'])) {
    $searchTerm = $_GET['meal'];
    
    $sql = "SELECT * FROM dishes WHERE name LIKE ? OR ingredients LIKE ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $searchTermWithWildcards = "%" . $searchTerm . "%"; 
        $stmt->bind_param("ss", $searchTermWithWildcards, $searchTermWithWildcards);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h2>Search Results:</h2>";
            while ($row = $result->fetch_assoc()) {
                echo "<p><strong>" . $row['name'] . "</strong> - " . $row['description'] . "</p>";
            }
        } else {
            echo "<p>No results found. Please try again with a different search term.</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Oops! Something went wrong. Please try again later.</p>";
    }
} else {
    echo "<p>Please enter a search term to begin your search.</p>";
}

$conn->close();
?>
