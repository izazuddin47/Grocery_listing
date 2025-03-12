<?php
$host = "127.0.0.1";
$username = "root";
$password = "";
$db = "grocery_list";

// Correcting the connection function
$conn = mysqli_connect($host, $username, $password, $db);

// Checking if connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
session_start();

?>
