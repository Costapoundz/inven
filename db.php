<?php
$host = "localhost";
$dbname = "new";
$username = "root";
$password = "";

try {
    // Create a PDO instance and store it in $pdo
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optional: Confirm connection
    // echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
