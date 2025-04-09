<?php

$host = "localhost"; // Or your server's IP address
$port = 3306; // Default MySQL port
$user = "root";
$password = "";
$dbname = "gamevault";

try {
    $pdo = new PDO($host, $port, $user, $password, $dbname);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection Failed" . $e->getMessage();
}