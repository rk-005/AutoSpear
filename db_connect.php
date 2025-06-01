<?php
// Database connection parameters
$servername = "localhost";
$username = "root";    // Default XAMPP MySQL username
$password = "";        // Default XAMPP MySQL password (empty)
$dbname = "vulnerable_app"; // Your database name

// Optional: If you changed the MySQL port in XAMPP's my.ini (e.g., to 3307)
// $port = 3307; 

// Create connection
// If you changed the port, add $port as the fifth argument: new mysqli($servername, $username, $password, $dbname, $port);
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Crucial for debugging: If connection fails, this will output the error.
    die("Connection failed: " . $conn->connect_error); 
}
?>