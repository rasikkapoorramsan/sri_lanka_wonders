<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = ""; // Change this if you have a password set
$dbname = "wonders";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>