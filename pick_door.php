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

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bet_amount']) && isset($_POST['door'])) {
    $bet_amount = floatval($_POST['bet_amount']);
    $chosen_door = intval($_POST['door']);

    if ($bet_amount > $wallet) {
        $response = ["status" => "error", "message" => "Insufficient Balance!"];
    } else {
        mysqli_query($conn, "UPDATE users SET wallet = wallet - $bet_amount WHERE user_id = '$user_id'");

        // **Updated Probability**
        $randomNumber = rand(1, 100);
        if ($randomNumber <= 60) { // 60% chance to Lose
            $result = "Lose";
            $multiplier = 0;
        } elseif ($randomNumber <= 90) { // 30% chance to win 2x
            $result = "Small Win";
            $multiplier = 2;
        } else { // 10% chance to win 5x (Jackpot)
            $result = "Big Win";
            $multiplier = 5;
        }

        $winnings = $bet_amount * $multiplier;
        mysqli_query($conn, "UPDATE users SET wallet = wallet + $winnings WHERE user_id = '$user_id'");

        // Save bet in DB
        $stmt = mysqli_prepare($conn, "INSERT INTO pick_door_bets (user_id, amount, chosen_door, result, winnings) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sdisd", $user_id, $bet_amount, $chosen_door, $result, $winnings);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $response = [
            "status" => "success",
            "chosen_door" => $chosen_door,
            "result" => $result,
            "winnings" => $winnings
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
    <title>Pick a Door</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <style>
        body { background-color: #FBF8EF; font-family: Arial, sans-serif; text-align: center; }
        .container { max-width: 400px; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        
        .door-container { display: flex; justify-content: space-between; margin: 20px 0; }
        .door { width: 100px; height: 150px; background: #80CBC4; border-radius: 5px; cursor: pointer; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: transform 0.5s ease, background 0.3s; }
        .door:hover { background: #B4EBE6; transform: scale(1.1); }
        .door.selected { background: #FFB433; transform: scale(1.2); }
        .door.revealed { background: #FF5733; }

        .bet-input { width: 100%; margin-top: 15px; }
        .pick-btn { background: #FFB433; color: black; font-size: 18px; padding: 10px; width: 100%; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h2>🚪 Pick a Door</h2>
    <p>💰 Wallet: ₹<span id="wallet"><?= number_format($wallet, 2) ?></span></p>

    <input type="number" id="bet_amount" class="form-control bet-input" placeholder="Enter Bet Amount" required>
    
    <div class="door-container">
        <div class="door" id="door1" onclick="selectDoor(1)">🚪</div>
        <div class="door" id="door2" onclick="selectDoor(2)">🚪</div>
        <div class="door" id="door3" onclick="selectDoor(3)">🚪</div>
    </div>

    <button onclick="submitBet()" class="btn pick-btn">CHOOSE THIS DOOR</button>
</div>
<div class="container mt-4">
    <h3>🏆 Leaderboard - Top 5 Players 🏆</h3>
    <table class="table table-bordered">
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
<script>
// List of 50+ Fake Player Names
const playerNames = [
    "Rahul99", "AnjaliX", "LuckyStar", "RohanPro", "GamerX", "Neha_Win", "SharmaJi", "KingOfLuck", "Prince77", "BossGamer",
    "SuperStarX", "QueenBee", "Mithun_777", "Shakti22", "VinayThePro", "MasterMind", "ArjunHero", "RockyWin", "LuckyGamer23",
    "PowerPlay99", "ManishCrush", "ChhotaDon", "Diva_Win", "TechNoob", "WinnerQueen", "NightKing", "SinghSaab", "VikramX",
    "Warrior007", "HackerGamer", "Killer_91", "DancerDude", "Sneha_Star", "AnmolWinner", "Legend_777", "VictoryRider",
    "Armaan22", "ShivamKiller", "GameChanger", "LuckyBoss", "MagicKing", "SneakyPlayer", "SandeepLotto", "MahiSuper",
    "GameGod", "Akash_Winner", "PayalLucky", "ShreyaPro", "BhawnaQueen", "BigJackpot", "SuperPro99"
];

// Store today's player list (Randomized once per day)
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

// Function to update leaderboard & refresh winnings
function updateLeaderboard() {
    // Shuffle winnings slightly each update
    leaderboardEntries.forEach(player => {
        player.winnings += Math.floor(Math.random() * 5000) - 2000; // Small random increase/decrease
        if (player.winnings < 0) player.winnings = 0; // No negative winnings
    });

    // Sort by highest winnings
    leaderboardEntries.sort((a, b) => b.winnings - a.winnings);

    // Update leaderboard HTML
    let leaderboardHTML = "";
    leaderboardEntries.forEach((player, index) => {
        leaderboardHTML += `
            <tr>
                <td>#${index + 1}</td>
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

<script>
let selectedDoor = null;

function showToast(message, type) {
    Swal.fire({ title: message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, icon: type });
}

function selectDoor(doorNumber) {
    selectedDoor = doorNumber;
    
    document.querySelectorAll(".door").forEach(door => door.classList.remove("selected"));
    document.getElementById("door" + doorNumber).classList.add("selected");
}

function revealDoor(doorNumber, result, winnings) {
    let door = document.getElementById("door" + doorNumber);
    door.classList.add("revealed");

    setTimeout(() => {
        Swal.fire(`🎉 ${result}`, `You won ₹${winnings.toFixed(2)}!`, result === "Lose" ? "error" : "success");
        setTimeout(() => { location.reload(); }, 2000);
    }, 1000);
}

function submitBet() {
    let betAmount = parseFloat($("#bet_amount").val());

    if (!selectedDoor) {
        showToast("Select a door first!", "error");
        return;
    }

    if (isNaN(betAmount) || betAmount <= 0) {
        showToast("Enter a valid bet amount!", "error");
        return;
    }

    $.post("", { bet_amount: betAmount, door: selectedDoor }, function(response) {
        let data = JSON.parse(response);

        if (data.status === "error") {
            showToast(data.message, "error");
        } else {
            revealDoor(data.chosen_door, data.result, data.winnings);
        }
    });
}
</script>

</body>
</html>
