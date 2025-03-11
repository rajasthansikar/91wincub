<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user balance
$query = "SELECT wallet FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $wallet);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lucky Number Spin 🎡</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #ABF62D; font-family: Arial, sans-serif; text-align: center; color: black; }
        .container { max-width: 400px; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); }
        
        .wheel-box {
            width: 200px;
            height: 200px;
            background: #D6A3FB;
            margin: 20px auto;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
            color: white;
            transition: transform 3s ease-out;
        }

        .bet-input { width: 100%; margin-top: 15px; padding: 10px; border-radius: 5px; text-align: center; border: 2px solid black; }

        .game-btn { font-size: 18px; padding: 10px; width: 100%; margin-top: 15px; border-radius: 5px; border: none; cursor: pointer; }
        .btn-spin { background: #D6A3FB; color: white; }
        .btn-spin:hover { background: #B374E4; }
    </style>
</head>
<body>

<div class="container">
    <h2>🎡 Lucky Number Spin</h2>
    <p>💰 Wallet: ₹<span id="wallet"><?= number_format($wallet, 2) ?></span></p>

    <input type="number" id="bet_amount" class="form-control bet-input" placeholder="Enter Bet Amount" required>
    
    <select id="chosen_number" class="form-control bet-input">
        <option value="">Select Your Number</option>
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select>

    <button onclick="startSpin()" class="btn game-btn btn-spin">🎰 Spin the Wheel</button>

    <div class="wheel-box" id="wheel">❓</div>
</div>

<script>
let betAmount = 0;

function showToast(message, type) {
    Swal.fire({ title: message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, icon: type });
}

function spinWheel() {
    return Math.floor(Math.random() * 10) + 1;
}

function startSpin() {
    betAmount = parseFloat(document.getElementById("bet_amount").value);
    let chosenNumber = parseInt(document.getElementById("chosen_number").value);

    if (!betAmount || betAmount <= 0 || isNaN(chosenNumber)) {
        showToast("Enter a valid bet amount & choose a number!", "error");
        return;
    }

    let formData = new FormData();
    formData.append("action", "withdraw");
    formData.append("amount", betAmount);

    fetch("update_wallet.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            showToast(data.error, "error");
        } else {
            document.getElementById("wallet").textContent = data.wallet;
            let spinResult = spinWheel();
            
            let wheel = document.getElementById("wheel");
            wheel.style.transform = "rotate(" + (360 * 5 + spinResult * 36) + "deg)";
            setTimeout(() => {
                wheel.innerHTML = spinResult;
                checkWin(chosenNumber, spinResult);
            }, 3000);
        }
    })
    .catch(error => showToast("Failed to connect to server!", "error"));
}

function checkWin(chosenNumber, spinResult) {
    let winnings = 0;
    
    if (chosenNumber === spinResult) {
        winnings = betAmount * 8; // Correct guess = 8x payout
        showToast(`🎉 You Won! Winning: ₹${winnings.toFixed(2)}`, "success");
    } else if (Math.abs(chosenNumber - spinResult) === 1) {
        winnings = betAmount * 1.5; // One number away = 1.5x payout
        showToast(`⚡ So close! Small Win: ₹${winnings.toFixed(2)}`, "info");
    } else {
        showToast("💥 You Lost! Better luck next time!", "error");
    }

    let depositFormData = new FormData();
    depositFormData.append("action", "deposit");
    depositFormData.append("amount", winnings);

    fetch("update_wallet.php", {
        method: "POST",
        body: depositFormData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById("wallet").textContent = data.wallet;
    });
}
</script>

</body>
</html>
