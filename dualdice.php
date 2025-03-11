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
    <title>Dice Duel 🎲 - High-Low Bet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { max-width: 400px; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); }

        .dice-box {
            font-size: 50px;
            background: #FFE900;
            padding: 20px;
            width: 100px;
            height: 100px;
            margin: 20px auto;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
        }

        .bet-input { width: 100%; margin-top: 15px; padding: 10px; border-radius: 5px; text-align: center; border: 2px solid #BDBCB8; }

        .game-btn { font-size: 18px; padding: 10px; width: 100%; margin-top: 15px; border-radius: 5px; border: none; color: white; transition: 0.3s ease; cursor: pointer; }
        .btn-high { background: #FF6B01; }
        .btn-low { background: #353535; }
        .btn-high:hover { background: #E05B00; }
        .btn-low:hover { background: #222222; }
    </style>
    <style>
        body { background-color:rgb(255, 255, 255); font-family: Arial, sans-serif; text-align: center; }
        .container { max-width: 400px; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); }
        
        .leaderboard {
            background: #ffcc00;
            padding: 10px;
            border-radius: 10px;
        }
        
        .leaderboard h2 { color: #353535; }
        .leaderboard table { width: 100%; margin-top: 10px; }
        .leaderboard th, .leaderboard td {
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #fff;
        }
        
        .leaderboard tr:nth-child(odd) { background: #fff6d5; }
        .leaderboard tr:nth-child(even) { background: #ffe08a; }

        .btn-back { background: #f92c85; color: white; font-size: 16px; padding: 10px; width: 100%; margin-top: 15px; border-radius: 5px; border: none; cursor: pointer; }
        .btn-back:hover { background: #e02070; }
    </style>
</head>
<body>

<div class="container">
    <h2>🎲 Dice Duel - High-Low Bet</h2>
    <p>💰 Wallet: ₹<span id="wallet"><?= number_format($wallet, 2) ?></span></p>

    <input type="number" id="bet_amount" class="form-control bet-input" placeholder="Enter Bet Amount" required>
    
    <button onclick="placeBet('high')" class="btn game-btn btn-high">🔺 Bet on High (4-6)</button>
    <button onclick="placeBet('low')" class="btn game-btn btn-low">🔻 Bet on Low (1-3)</button>

    <div class="dice-box" id="dice">🎲</div>
</div>
<div class="container">
    

    <div class="leaderboard">
        <h2>🔥 Top Winners</h2>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Total Winnings</th>
                </tr>
            </thead>
            <tbody id="leaderboard-body">
                <!-- Fake entries will be injected here -->
            </tbody>
        </table>
    </div>
    <script>
    const fakePlayers = [
        "Rahul_91", "Swayam_007", "Neha_X", "LuckyKing", "Pardeep98",
        "Rohit_K", "MeghaQueen", "Arjun_OP", "Payal_M", "SharmaX",
        "Deepak_11", "NitinBoss", "RajeshLucky", "Vikas999", "PreetiS",
        "Sakshi_777", "ManojGuru", "DivyaStar", "Varun_GG", "AnjaliWins"
    ];

    function getRandomWinnings() {
        return (Math.random() * 5000 + 1000).toFixed(2); // Random between ₹1000 - ₹6000
    }

    function shuffleLeaderboard() {
        let shuffledPlayers = fakePlayers.sort(() => 0.5 - Math.random()).slice(0, 10);
        let leaderboardHTML = "";

        shuffledPlayers.forEach((player, index) => {
            let winnings = getRandomWinnings();
            leaderboardHTML += `
                <tr>
                    <td>#${index + 1}</td>
                    <td>${player}</td>
                    <td>₹${winnings}</td>
                </tr>
            `;
        });

        document.getElementById("leaderboard-body").innerHTML = leaderboardHTML;
    }

    // Shuffle leaderboard every 5 seconds
    setInterval(shuffleLeaderboard, 5000);
    
    // Load first leaderboard instantly
    shuffleLeaderboard();
</script>
<script>
let betAmount = 0;

function showToast(message, type) {
    Swal.fire({ title: message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, icon: type });
}

function rollDice() {
    return Math.floor(Math.random() * 6) + 1;
}

function placeBet(choice) {
    betAmount = parseFloat(document.getElementById("bet_amount").value);
    if (!betAmount || betAmount <= 0) {
        showToast("Enter a valid bet amount!", "error");
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
            let diceResult = rollDice();
            document.getElementById("dice").innerHTML = diceResult;

            let isWin = (choice === "high" && diceResult >= 4) || (choice === "low" && diceResult <= 3);
            let winnings = isWin ? betAmount * 1.9 : 0;

            if (isWin) {
                if (diceResult === 6) {
                    winnings *= 2; // Bonus Jackpot if rolling 6
                    showToast(`🔥 JACKPOT! You rolled a 6! Winning: ₹${winnings.toFixed(2)}`, "success");
                } else {
                    showToast(`🎉 You Won! Winning: ₹${winnings.toFixed(2)}`, "success");
                }
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
    })
    .catch(error => showToast("Failed to connect to server!", "error"));
}
</script>

</body>
</html>
