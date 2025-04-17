<?php

$servername = "localhost";
$port = 3306;
$username = "root";
$password = "root";
$database = "gamevault";

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
