<?php

$host = "localhost";
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