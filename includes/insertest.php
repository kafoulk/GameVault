<?php

// insert_test.php

$host = "localhost";
$port = 3306;
$user = "root";
$password = "";
$dbname = "gamevault";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sample SQL query to insert a new user
    $sql = "INSERT INTO users (full_name, email) VALUES (:full_name, :email)";

    // Prepare the SQL query
    $stmt = $pdo->prepare($sql);

    // Bind parameters and execute
    $stmt->execute([
        ':full_name' => 'John Doe',
        ':email' => 'johndoe@example.com'
    ]);

    echo "New user added successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

