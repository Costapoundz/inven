<?php
require 'db.php';

$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;
$total = $_POST['total'] ?? 0;

if (!$name || !$price || !$quantity || !$total) {
    die("Invalid data received.");
}

// ✅ Always insert a new record, no checking for existing items
$stmt = $pdo->prepare("INSERT INTO scanned_items (name, price, quantity, total) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $price, $quantity, $total]);

echo "Item recorded successfully.";
?>
