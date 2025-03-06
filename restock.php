<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'fetch') {
        // Fetch item details
        $code = $_POST['code'] ?? '';

        if (!$code) {
            echo json_encode(["success" => false, "message" => "Item code is required."]);
            exit;
        }

        $stmt = $pdo->prepare("SELECT name, price, stock FROM inventory WHERE code = ?");
        $stmt->execute([$code]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            echo json_encode(["success" => true, "name" => $item['name'], "price" => $item['price'], "stock" => $item['stock']]);
        } else {
            echo json_encode(["success" => false, "message" => "Item not found."]);
        }
    } elseif ($action === 'restock') {
        // Handle restocking
        $code = $_POST['code'] ?? '';
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $quantity = (int) ($_POST['quantity'] ?? 0);

        if (!$code || !$name || !$price || $quantity <= 0) {
            echo json_encode(["success" => false, "message" => "Invalid input data."]);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Fetch current stock
            $stmt = $pdo->prepare("SELECT stock FROM inventory WHERE code = ?");
            $stmt->execute([$code]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$item) {
                throw new Exception("Item not found.");
            }

            $currentStock = (int) $item['stock'];
            $newStock = $currentStock + $quantity;

            // Update stock quantity and price
            $updateStmt = $pdo->prepare("UPDATE inventory SET stock = ?, price = ? WHERE code = ?");
            $updateStmt->execute([$newStock, $price, $code]);

            // Log restocking transaction
            $transactionStmt = $pdo->prepare("
                INSERT INTO transactions (code, name, quantity, transaction_type, previous_stock, new_stock, timestamp) 
                VALUES (?, ?, ?, 'addition', ?, ?, NOW())
            ");
            $transactionStmt->execute([$code, $name, $quantity, $currentStock, $newStock]);

            $pdo->commit();

            echo json_encode(["success" => true, "message" => "Restocked successfully! New stock for $name: $newStock."]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>