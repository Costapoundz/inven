<?php
require "db.php"; // Include the database connection

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST["code"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];

    if (empty($code) || empty($name) || empty($price) || empty($quantity)) {
        $message = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM inventory WHERE code = :code");
        $stmt->execute(["code" => $code]);

        if ($stmt->fetch()) {
            $message = "Item with code $code already exists.";
        } else {
            $sql = "INSERT INTO inventory (code, name, price, quantity) VALUES (:code, :name, :price, :quantity)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute(["code" => $code, "name" => $name, "price" => $price, "quantity" => $quantity])) {
                $message = "Item <strong>$name</strong> with code <strong>$code</strong> has been added to the inventory.";
                $success = true;
            } else {
                $message = "Item not added. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card p-4">
            <h2 class="text-center mb-4">Add New Item</h2>
            <form action="add_item.php" method="POST">
                <div class="mb-3">
                    <label for="code" class="form-label">Code:</label>
                    <input type="text" id="code" name="code" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price:</label>
                    <input type="number" id="price" name="price" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add Item</button>
            </form>
        </div>
    </div>

    <!-- Modal Popup -->
    <?php if (!empty($message)) : ?>
    <div class="modal fade show d-block" id="messageModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <h2 class="<?php echo $success ? 'text-success' : 'text-danger'; ?>">
                    <?php echo $success ? "Success!" : "Error!"; ?>
                </h2>
                <p><?php echo $message; ?></p>
                <button class="btn btn-success" onclick="redirectToDashboard()">Go to Dashboard</button>
                <button class="btn btn-primary" onclick="redirectToAddItem()">Add Another Item</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        function redirectToDashboard() {
           
            window.location.href = "dashboard.php";
        }
        function redirectToAddItem() {
            let redirectWindow = window.open("", "_blank");

            window.location.href = "add_item.php";

            
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



   
