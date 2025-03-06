<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Scanner</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 600px; margin-top: 50px; }
        .card { padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        .table th, .table td { text-align: center; }
    </style>
</head>
<body>
  
<button on click ="go()">back</button>

<div class="container">
    <div class="card">
        <h2 class="text-center mb-4">Inventory Scanner</h2>
        <input type="text" id="barcode" class="form-control mb-3" placeholder="Scan barcode here" autofocus>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Item Name</th>
                    <th>Unit Price (GH₵)</th>
                    <th>Quantity</th>
                    <th>Total (GH₵)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="itemTable"></tbody>
        </table>

        <h4 class="text-end">Grand Total: GH₵ <span id="grandTotal">0.00</span></h4>

        <button class="btn btn-success w-100 mt-3" onclick="confirmTransaction()">Done</button>
    </div>
</div>

<script>
let itemList = [];

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('barcode').addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            fetchItem();
        }
    });
});

function fetchItem() {
    let barcode = document.getElementById('barcode').value.trim();
    if (barcode === '') return;

    fetch(`fetch_item.php?code=${barcode}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                addItemToList(data.name, parseFloat(data.price));
            }
            document.getElementById('barcode').value = '';
        })
        .catch(error => alert("Error fetching item."));
}

function addItemToList(name, price) {
    let itemTable = document.getElementById("itemTable");
    let row = itemTable.insertRow();
    let quantity = 1;

    row.insertCell(0).innerText = name;
    row.insertCell(1).innerText = price.toFixed(2);

    let quantityCell = row.insertCell(2);
    let rowTotalCell = row.insertCell(3);
    let removeCell = row.insertCell(4);

    let quantityInput = document.createElement("input");
    quantityInput.type = "number";
    quantityInput.value = quantity;
    quantityInput.min = "1";
    quantityInput.classList.add("form-control");
    quantityInput.style.width = "70px";

    quantityInput.addEventListener("input", function () {
        updateRowTotal(rowTotalCell, quantityInput.value, price);
    });

    quantityCell.appendChild(quantityInput);
    rowTotalCell.innerText = price.toFixed(2);

    let removeBtn = document.createElement("button");
    removeBtn.innerText = "Remove";
    removeBtn.classList.add("btn", "btn-danger", "btn-sm");
    removeBtn.onclick = function () {
        removeItem(row);
    };

    removeCell.appendChild(removeBtn);

    itemList.push({ name, price, quantityInput });
    updateGrandTotal();
}

function saveToDatabase(name, price, quantity, total) {
    let formData = new FormData();
    formData.append("name", name);
    formData.append("price", price);
    formData.append("quantity", quantity);
    formData.append("total", total);

    fetch("save_scan.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => console.log(data))
    .catch(error => console.error("Error:", error));
}

function updateRowTotal(rowTotalCell, quantity, price) {
    let rowTotal = quantity * price;
    rowTotalCell.innerText = rowTotal.toFixed(2);
    updateGrandTotal();
}

function updateGrandTotal() {
    let grandTotal = itemList.reduce((sum, item) => sum + (parseFloat(item.quantityInput.value) * item.price), 0);
    document.getElementById("grandTotal").innerText = grandTotal.toFixed(2);
}

function removeItem(row) {
    row.remove();
    itemList = itemList.filter(item => item.name !== row.cells[0].innerText);
    updateGrandTotal();
}

function confirmTransaction() {
    itemList.forEach((item) => {
        let finalQuantity = item.quantityInput.value;
        let total = finalQuantity * item.price;

        saveToDatabase(item.name, item.price, finalQuantity, total);
    });

    // Show success prompt with print & dashboard options
    showSuccessPrompt();
}

function showSuccessPrompt() {
    let confirmationDiv = document.createElement("div");
    confirmationDiv.innerHTML = `
        <div class="modal fade" id="successModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transaction Successful!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>Your transaction has been saved successfully.</p>
                        <button class="btn btn-primary" onclick="printInvoice()">Print</button>
                        <button class="btn btn-secondary" onclick="goToDashboard()">Go to Dashboard</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(confirmationDiv);
    let successModal = new bootstrap.Modal(document.getElementById("successModal"));
    successModal.show();
}

// Function to open print preview
function printInvoice() {
    let invoiceWindow = window.open("", "_blank");

    let companyName = "Your Company Name"; // Change this to your actual company name
    let companyPhone = "Your Phone Number"; // Change this to your company phone number

    invoiceWindow.document.write(`
        <html>
        <head>
            <title>Invoice</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; }
                h2 { margin-bottom: 5px; }
                p { margin-top: 0; font-size: 14px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid black; padding: 8px; text-align: center; }
                th { background-color: #f2f2f2; }
                .total { font-weight: bold; }
            </style>
        </head>
        <body>
      
            <h2>${companyName}</h2>
            <p>Contact: ${companyPhone}</p>
            
            <table>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Total (GH₵)</th>
                </tr>
    `);

    let grandTotal = 0;

    itemList.forEach(item => {
        let finalQuantity = item.quantityInput.value;
        let itemTotal = finalQuantity * item.price;
        grandTotal += itemTotal;

        invoiceWindow.document.write(`
            <tr>
                <td>${item.name}</td>
                <td>${finalQuantity}</td>
                <td>GH₵ ${itemTotal.toFixed(2)}</td>
            </tr>
        `);
    });

    invoiceWindow.document.write(`
            </table>
            <h3 class="total">Grand Total: GH₵ ${grandTotal.toFixed(2)}</h3>
        </body>
        </html>
    `);

    invoiceWindow.document.close();
    invoiceWindow.print();
}


// Function to redirect to dashboard
function goToDashboard() {
    window.location.href = "dashboard.php"; // Update with your dashboard page
}

  
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
