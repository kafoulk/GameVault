<?php

$host = "localhost";
$port = 3306;
$user = "root";
$password = "root";
$dbname = "gamevault";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>