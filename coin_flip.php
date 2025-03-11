<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch User Wallet Balance & VIP Level
$query = "SELECT wallet, total_bets, vip_level FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $wallet, $total_bets, $vip_level);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// **VIP Levels Based on Total Bets**
$vipMultipliers = [
    1 => 1.9,  // Level 1: Default
    2 => 2.0,  // Level 2: ₹5,000+ total bets
    3 => 2.1   // Level 3: ₹20,000+ total bets
];

// **Upgrade VIP Level Automatically**
if ($total_bets >= 20000) {
    $vip_level = 3;
} elseif ($total_bets >= 5000) {
    $vip_level = 2;
}

// **Update VIP Level in Database**
mysqli_query($conn, "UPDATE users SET vip_level = $vip_level WHERE user_id = '$user_id'");

$response = [];

// **Handle Coin Flip Bet**
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bet_amount']) && isset($_POST['choice'])) {
    $bet_amount = floatval($_POST['bet_amount']);
    $player_choice = $_POST['choice'];

    if ($bet_amount > $wallet) {
        $response = ["status" => "error", "message" => "Insufficient Balance!"];
    } else {
        // Deduct Bet from Wallet
        mysqli_query($conn, "UPDATE users SET wallet = wallet - $bet_amount, total_bets = total_bets + $bet_amount WHERE user_id = '$user_id'");

        // Flip the Coin (50/50 Chance)
        $flip_result = (rand(0, 1) == 0) ? "Heads" : "Tails";

        // **Get User's Current Payout Multiplier**
        $payoutMultiplier = $vipMultipliers[$vip_level];

        // Determine Win/Loss
        if ($player_choice === $flip_result) {
            $status = "Win";
            $winnings = $bet_amount * $payoutMultiplier;
            mysqli_query($conn, "UPDATE users SET wallet = wallet + $winnings WHERE user_id = '$user_id'");
        } else {
            $status = "Lose";
            $winnings = 0;
        }

        // Save Bet in Database
        $stmt = mysqli_prepare($conn, "INSERT INTO coin_flip_bets (user_id, amount, choice, result, status, winnings) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sdsdsd", $user_id, $bet_amount, $player_choice, $flip_result, $status, $winnings);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $response = [
            "status" => "success",
            "choice" => $player_choice,
            "result" => $flip_result,
            "winnings" => $winnings,
            "game_status" => $status,
            "vip_level" => $vip_level,
            "payout_multiplier" => $payoutMultiplier
        ];
    }
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coin Flip Betting Game - VIP & Double or Nothing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <style>
        body { background-color:rgb(255, 255, 255); font-family: Arial, sans-serif; text-align: center; }
        .container { max-width: 400px; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        
        .coin-container { display: flex; justify-content: space-around; margin: 20px 0; }
        .coin { width: 120px; height: 120px; background: #ECC232; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 24px; font-weight: bold; transition: transform 0.3s ease, background 0.3s; }
        .coin:hover { background: #FFE900; transform: scale(1.1); }
        .coin.selected { background: #FFE900; transform: scale(1.2); }

        .bet-input { width: 100%; margin-top: 15px; }
        .flip-btn { background: #ECC232; color: black; font-size: 18px; padding: 10px; width: 100%; margin-top: 15px; }
        .double-btn { background: #FF4500; color: white; font-size: 18px; padding: 10px; width: 100%; margin-top: 15px; display: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>🪙 Coin Flip - VIP Level <?= $vip_level ?> 🎖️</h2>
    <p>💰 Wallet: ₹<span id="wallet"><?= number_format($wallet, 2) ?></span></p>
    <p>🔥 VIP Level: <?= $vip_level ?> (Payout: <?= $vipMultipliers[$vip_level] ?>x)</p>

    <input type="number" id="bet_amount" class="form-control bet-input" placeholder="Enter Bet Amount" required>
    
    <div class="coin-container">
        <div class="coin" id="heads" onclick="selectChoice('Heads')">Heads</div>
        <div class="coin" id="tails" onclick="selectChoice('Tails')">Tails</div>
    </div>

    <button onclick="submitBet()" class="btn flip-btn">FLIP COIN</button>
    <button onclick="doubleOrNothing()" class="btn double-btn" id="doubleBtn">DOUBLE OR NOTHING</button>
</div>
<div class="container mt-4">
    <h3 class="leaderboard-title">🏆 Top Winners Leaderboard 🏆</h3>
    <div class="leaderboard-container">
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Total Winnings</th>
                </tr>
            </thead>
            <tbody id="leaderboard">
                <!-- Fake data will be inserted here -->
            </tbody>
        </table>
    </div>
</div>
<style>
    /* Leaderboard Container */
    .leaderboard-container {
        max-width: 400px;
        margin: 30px auto;
        background: linear-gradient(145deg, #1e1e1e, #2a2a2a);
        padding: 15px;
        border-radius: 15px;
        box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.3), -4px -4px 10px rgba(50, 50, 50, 0.2);
        text-align: center;
        color: #fff;
        font-family: "Arial", sans-serif;
    }

    /* Leaderboard Title */
    .leaderboard-title {
        font-size: 24px;
        font-weight: bold;
        color: #FFE900;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    /* Leaderboard Box */
    .leaderboard-box {
        border-radius: 10px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.1);
    }

    /* Table Styles */
    .leaderboard-table {
        width: 100%;
        text-align: center;
        border-collapse: collapse;
        color: #fff;
    }

    .leaderboard-table th {
        background: #ECC232;
        color: #222;
        padding: 10px;
        border-radius: 5px;
        font-size: 16px;
    }

    .leaderboard-table td {
        padding: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 15px;
        transition: all 0.3s ease;
    }

    /* Rank Styling */
    .gold { color: gold; font-weight: bold; }
    .silver { color: silver; font-weight: bold; }
    .bronze { color: #CD7F32; font-weight: bold; }

    .rank-badge {
        font-weight: bold;
        padding: 5px 10px;
        border-radius: 5px;
    }

    /* Animated Updates */
    .animate-winner {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        0% { transform: scale(0.9); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Hover Effect */
    .leaderboard-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.1);
    }
</style>


<script>
let selectedChoice = null;
let lastWin = false;

function selectChoice(choice) {
    selectedChoice = choice;
    document.querySelectorAll(".coin").forEach(coin => coin.classList.remove("selected"));
    document.getElementById(choice.toLowerCase()).classList.add("selected");
}

function submitBet() {
    let betAmount = parseFloat($("#bet_amount").val());
    if (!selectedChoice || isNaN(betAmount) || betAmount <= 0) {
        Swal.fire("Error", "Select Heads or Tails & enter a valid amount!", "error");
        return;
    }

    $.post("", { bet_amount: betAmount, choice: selectedChoice }, function(response) {
        let data = JSON.parse(response);
        Swal.fire(`🎉 ${data.choice} vs ${data.result}`, `You ${data.game_status}! Winnings: ₹${data.winnings}`, data.game_status === "Win" ? "success" : "error");
        lastWin = data.game_status === "Win";
        document.getElementById("doubleBtn").style.display = lastWin ? "block" : "none";
    });
}

function doubleOrNothing() {
    submitBet(); // Re-run bet with winnings
}
</script>
<script>
// List of Fake Players
const playerNames = [
    "Rahul99", "AnjaliX", "LuckyStar", "RohanPro", "GamerX", "Neha_Win", "SharmaJi", "KingOfLuck", "Prince77", "BossGamer",
    "SuperStarX", "QueenBee", "Mithun_777", "Shakti22", "VinayThePro", "MasterMind", "ArjunHero", "RockyWin", "LuckyGamer23"
];

// Store Today's Player List (Randomized Once Per Day)
let dailyPlayers = [];
function setDailyPlayers() {
    let dateKey = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
    if (localStorage.getItem("leaderboardDate") !== dateKey) {
        localStorage.setItem("leaderboardDate", dateKey);
        dailyPlayers = [...playerNames].sort(() => Math.random() - 0.5).slice(0, 10); // Pick 10 Random Names
        localStorage.setItem("dailyPlayers", JSON.stringify(dailyPlayers));
    } else {
        dailyPlayers = JSON.parse(localStorage.getItem("dailyPlayers"));
    }
}

// Initialize Daily Players
setDailyPlayers();

// Generate 5 Leaderboard Entries
let leaderboardEntries = dailyPlayers.slice(0, 5).map(name => ({
    name,
    winnings: Math.floor(Math.random() * 100000) + 5000 // Random winnings (₹5,000 - ₹100,000)
}));

// Function to Update Leaderboard
function updateLeaderboard() {
    // Shuffle winnings slightly each update
    leaderboardEntries.forEach(player => {
        player.winnings += Math.floor(Math.random() * 5000) - 2000; // Small random increase/decrease
        if (player.winnings < 0) player.winnings = 0; // Prevent negative values
    });

    // Sort by highest winnings
    leaderboardEntries.sort((a, b) => b.winnings - a.winnings);

    // Update leaderboard HTML
    let leaderboardHTML = "";
    leaderboardEntries.forEach((player, index) => {
        let rankClass = index === 0 ? "gold" : index === 1 ? "silver" : index === 2 ? "bronze" : "";
        let rankBadge = index === 0 ? "🥇" : index === 1 ? "🥈" : index === 2 ? "🥉" : `#${index + 1}`;

        leaderboardHTML += `
            <tr class="animate-winner">
                <td class="rank-badge ${rankClass}">${rankBadge}</td>
                <td>${player.name}</td>
                <td>₹${player.winnings.toLocaleString()}</td>
            </tr>
        `;
    });

    document.getElementById("leaderboard").innerHTML = leaderboardHTML;
}

// Run immediately & update every 5 seconds
updateLeaderboard();
setInterval(updateLeaderboard, 5000);
</script>

</body>
</html>
