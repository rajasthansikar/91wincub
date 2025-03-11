<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wallet balance
$sql = "SELECT wallet FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $walletBalance);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Fetch transaction history
$transactions = [];
$sql = "SELECT type, amount, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $transactions[] = $row;
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet - 91Win</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .wallet-container {
            max-width: 400px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .wallet-balance {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #28a745;
        }
        .btn-wallet {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .transaction-history {
            margin-top: 20px;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="wallet-container">
    <h2><i class="fas fa-wallet"></i> My Wallet</h2>
    <p class="wallet-balance">Balance: ₹<span id="walletBalance"><?= number_format($walletBalance, 2) ?></span></p>

    <button class="btn btn-success btn-wallet" onclick="window.location.href='deposit.php'">Deposit Money</button>
<button class="btn btn-danger btn-wallet" onclick="window.location.href='withdraw.php'">Withdraw Money</button>

    <div class="transaction-history">
        <h4>Transaction History</h4>
        <ul id="transactionList" class="list-group">
            <?php foreach ($transactions as $txn): ?>
                <li class="list-group-item">
                    <b><?= ucfirst($txn['type']) ?>:</b> ₹<?= number_format($txn['amount'], 2) ?> 
                    <small class="text-muted">(<?= date('d M Y, H:i', strtotime($txn['created_at'])) ?>)</small>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>


    fetch('update_wallet.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action, amount })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById("walletBalance").innerText = data.wallet.toFixed(2);
            let list = document.getElementById("transactionList");
            let listItem = document.createElement("li");
            listItem.classList.add("list-group-item");
            listItem.innerHTML = `<b>${action.charAt(0).toUpperCase() + action.slice(1)}:</b> ₹${amount.toFixed(2)}`;
            list.prepend(listItem);
        } else {
            alert(data.error);
        }
    })
    .catch(error => console.error('Error:', error));

</script>

</body>
</html>
