<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .container {
            width: 300px;
            margin: auto;
        }
        button {
            display: left;
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007BFF;
            color: white;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Inventory Dashboard</h2>
    <div class="container">
        <button onclick="location.href='add_item.php'">Add New Item</button>
        <button onclick="location.href='scan_item.php'">Scan Item</button>
        <button onclick="location.href='inventory.php'">inventory</button>
        <button onclick="location.href='restock.html'">Restock</button>
        <button onclick="location.href='transition.php'">sales</button>
    </div>
</body>
</html>



