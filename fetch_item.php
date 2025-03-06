<?php
require 'db.php';

$code = $_GET['code'] ?? '';

if (!$code) {
    echo json_encode(["error" => "Invalid barcode"]);
    exit;
}

$stmt = $pdo->prepare("SELECT name, price FROM inventory WHERE code = ?");
$stmt->execute([$code]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    echo json_encode($item);
} else {
    echo json_encode(["error" => "Item not found"]);
}
?>
