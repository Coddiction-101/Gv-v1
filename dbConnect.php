<?php

session_start();

$servername = "localhost";
$username = "worldaca_user_AMPSIJ";
$password = "Worldacademia@123";
$dbname = "worldaca_AMPSIJ";


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    // Log the real error (for developers)
    error_log("Database connection failed: " . mysqli_connect_error());

    // Show generic message to users
    die("Something went wrong. Please try again later.");
}

// Set charset
mysqli_set_charset($conn, "utf8");
?>

