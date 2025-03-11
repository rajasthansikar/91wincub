<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user wallet balance
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
    <title>Three Chests, One Key</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #fdf5df; font-family: Arial, sans-serif; text-align: center; color: black; }
        .container { max-width: 400px; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); }

        .chest-container {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .chest {
            width: 100px;
            height: 100px;
            background: #ffcc00;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            color: black;
            transition: transform 0.5s ease, background 0.5s;
            position: relative;
        }

        .chest:hover { transform: scale(1.1); }
        .chest.open { background: #ff4444; transform: scale(1.2) rotateY(360deg); }
        .chest.win { background: #28a745 !important; }
        .chest.disabled { pointer-events: none; opacity: 0.5; }

        .bet-input { width: 100%; margin-top: 15px; padding: 10px; border-radius: 5px; text-align: center; border: 1px solid #ffcc00; }

        .game-btn { font-size: 18px; padding: 10px; width: 100%; margin-top: 15px; border-radius: 5px; border: none; color: white; transition: 0.3s ease; cursor: pointer; }
        .btn-primary { background: #f92c85; }
        .btn-primary:hover { background: #e02070; }

        /* Confetti effect */
        .confetti {
            position: fixed;
            top: 0;
            left: 50%;
            width: 100px;
            height: 100px;
            display: none;
            pointer-events: none;
            animation: confetti-fall 1.5s ease-out forwards;
        }

        @keyframes confetti-fall {
            0% { transform: translateY(-100px) scale(1); opacity: 1; }
            100% { transform: translateY(500px) scale(0.5); opacity: 0; }
        }
    </style>
    <button class="btn-leaderboard" onclick="window.location.href='chestleaderboard.php'">🏆 View Leaderboard</button>
</head>
<body>

<div class="container">
    <h2>🎁 Three Chests, One Key</h2>
    <p>💰 Wallet: ₹<span id="wallet"><?= number_format($wallet, 2) ?></span></p>

    <input type="number" id="bet_amount" class="form-control bet-input" placeholder="Enter Bet Amount" required>
    <button onclick="startGame()" class="btn game-btn btn-primary">Start Bet</button>

    <div class="chest-container">
        <div class="chest" id="chest1" onclick="openChest(1)">🗝️</div>
        <div class="chest" id="chest2" onclick="openChest(2)">🗝️</div>
        <div class="chest" id="chest3" onclick="openChest(3)">🗝️</div>
    </div>
</div>

<style>.btn-leaderboard {
    background: #ffcc00;
    color: black;
    font-size: 18px;
    padding: 10px;
    width: 100%;
    margin-top: 15px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-weight: bold;
}

.btn-leaderboard:hover {
    background: #ffd633;
}
</style>
<!-- Confetti Effect -->
<img src="https://cdn-icons-png.flaticon.com/512/477/477769.png" class="confetti" id="confetti">

<!-- Sound Effects -->
<audio id="winSound" src="https://www.fesliyanstudios.com/play-mp3/387"></audio>
<audio id="loseSound" src="https://www.fesliyanstudios.com/play-mp3/398"></audio>
<audio id="openSound" src="https://www.fesliyanstudios.com/play-mp3/425"></audio>

<script>
let betAmount = 0;
let winningChest = 0;
let multipliers = [0, 0, 1, 2, 5]; // House edge: 33% win rate
let gameActive = false;
let chestOpened = false;

function startGame() {
    if (gameActive) return;
    gameActive = true;
    chestOpened = false;

    resetChests();

    betAmount = parseFloat(document.getElementById("bet_amount").value);
    if (!betAmount || betAmount <= 0) {
        Swal.fire("Error", "Enter a valid bet amount!", "error");
        gameActive = false;
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
            Swal.fire("Error", data.error, "error");
            gameActive = false;
        } else {
            document.getElementById("wallet").textContent = data.wallet;
            winningChest = Math.floor(Math.random() * 3) + 1;
            Swal.fire("🔑 Choose a Chest!", "Click a chest to open!", "info");
        }
    });
}

function openChest(chestId) {
    if (!gameActive || chestOpened) return;
    chestOpened = true;

    document.querySelectorAll(".chest").forEach(chest => chest.classList.add("disabled"));
    let chest = document.getElementById("chest" + chestId);
    chest.classList.add("open");

    document.getElementById("openSound").play();

    setTimeout(() => {
        if (chestId === winningChest) {
            let multiplier = multipliers[Math.floor(Math.random() * multipliers.length)];
            let winnings = betAmount * multiplier;
            document.getElementById("winSound").play();

            let formData = new FormData();
            formData.append("action", "deposit");
            formData.append("amount", winnings);

            fetch("update_wallet.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("wallet").textContent = data.wallet;
                chest.classList.add("win");
                showConfetti();
                Swal.fire("🎉 You Won!", `Multiplier: ${multiplier}x \n Winnings: ₹${winnings.toFixed(2)}`, "success");
                setTimeout(resetGame, 2000);
            });
        } else {
            document.getElementById("loseSound").play();
            Swal.fire("💥 Empty Chest!", "Better luck next time!", "error");
            setTimeout(resetGame, 2000);
        }
    }, 800);
}

function resetGame() {
    gameActive = false;
    chestOpened = false;
    resetChests();
}

function resetChests() {
    document.querySelectorAll(".chest").forEach(chest => {
        chest.classList.remove("open", "win", "disabled");
    });
}

function showConfetti() {
    let confetti = document.getElementById("confetti");
    confetti.style.display = "block";
    setTimeout(() => confetti.style.display = "none", 1500);
}
</script>

</body>
</html>
