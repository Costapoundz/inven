<?php
require "db.php"; // Database connection

// Fetch all inventory items
$stmt = $pdo->query("SELECT * FROM inventory");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">Inventory List</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Code</th>
                <th>Item Name</th>
                <th>Price (GH₵)</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['code']) ?></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>GH₵ <?= number_format($item['price'], 2) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
]    </table>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
