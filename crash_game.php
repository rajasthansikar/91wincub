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

// Get last crash time & cooldown
$lastCrashQuery = "SELECT MAX(next_round_time) FROM crash_bets WHERE next_round_time IS NOT NULL";
$lastCrashResult = mysqli_query($conn, $lastCrashQuery);
$lastCrashTime = mysqli_fetch_row($lastCrashResult)[0] ?? null;

$cooldownActive = false;
$timeLeft = 0;
$nextRoundStart = strtotime($lastCrashTime) + 30;
$currentTime = time();

if ($lastCrashTime && $currentTime < $nextRoundStart) {
    $cooldownActive = true;
    $timeLeft = $nextRoundStart - $currentTime;
}

// Initialize session variables if not set
if (!isset($_SESSION['crash_multiplier'])) $_SESSION['crash_multiplier'] = 1.00;
if (!isset($_SESSION['bet_amount'])) $_SESSION['bet_amount'] = 0;
if (!isset($_SESSION['crash_time'])) $_SESSION['crash_time'] = 0;
if (!isset($_SESSION['cashout'])) $_SESSION['cashout'] = false;

// Handle bet placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bet_amount']) && !$cooldownActive) {
    $bet_amount = floatval($_POST['bet_amount']);

    if ($bet_amount > $wallet) {
        echo "<script>Swal.fire('Insufficient Balance!', 'You don’t have enough money to bet.', 'error');</script>";
    } else {
        mysqli_query($conn, "UPDATE users SET wallet = wallet - $bet_amount WHERE user_id = '$user_id'");

        $_SESSION['bet_amount'] = $bet_amount;
        $_SESSION['crash_multiplier'] = 1.00;
        $_SESSION['cashout'] = false;
        $_SESSION['crash_time'] = rand(120, 500) / 100;

        echo "<script>startCrashGame();</script>";
    }
}

// Handle cashout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cashout']) && $_SESSION['bet_amount'] > 0) {
    $_SESSION['cashout'] = true;
    $cashoutMultiplier = $_SESSION['crash_multiplier'];
    $winnings = $_SESSION['bet_amount'] * $cashoutMultiplier;

    mysqli_query($conn, "UPDATE users SET wallet = wallet + $winnings WHERE user_id = '$user_id'");

    // Save bet result
    $stmt = mysqli_prepare($conn, "INSERT INTO crash_bets (user_id, amount, cashout_multiplier, final_multiplier, status, next_round_time) 
                                   VALUES (?, ?, ?, ?, 'Cashed Out', NOW())");
    mysqli_stmt_bind_param($stmt, "sddd", $user_id, $_SESSION['bet_amount'], $cashoutMultiplier, $_SESSION['crash_time']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "<script>Swal.fire('You Cashed Out!', 'You won ₹" . number_format($winnings, 2) . "', 'success');</script>";

    unset($_SESSION['bet_amount'], $_SESSION['crash_multiplier'], $_SESSION['cashout'], $_SESSION['crash_time']);
}

// Fetch user's bet history
$historyQuery = "SELECT * FROM crash_bets WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$historyStmt = mysqli_prepare($conn, $historyQuery);
mysqli_stmt_bind_param($historyStmt, "s", $user_id);
mysqli_stmt_execute($historyStmt);
$historyResult = mysqli_stmt_get_result($historyStmt);
mysqli_stmt_close($historyStmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crash Game - Bet & Multiply</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #121212; font-family: Arial, sans-serif; color: white; }
        .game-container { max-width: 500px; margin: 20px auto; background: #1e1e1e; padding: 20px; border-radius: 10px; text-align: center; }
        .header { display: flex; justify-content: space-between; padding: 10px; background: #007bff; color: white; }
        .multiplier { font-size: 48px; font-weight: bold; transition: transform 0.1s; }
        .btn-cashout { background: #ffcc00; color: black; font-size: 18px; padding: 10px; margin-top: 10px; width: 100%; }
        .cooldown { font-size: 20px; font-weight: bold; color: red; }
    </style>
</head>
<body>

<div class="header">
    <span><strong>👤 <?= $user_id ?></strong></span>
    <span>💰 Wallet: ₹<span id="wallet"><?= number_format($wallet, 2) ?></span></span>
</div>

<div class="game-container">
    <h2>🚀 Crash Game</h2>

    <?php if ($cooldownActive): ?>
        <p class="cooldown">Next Round in: <span id="cooldown"><?= $timeLeft ?></span> sec</p>
    <?php else: ?>
        <canvas id="crashChart"></canvas>
        <p id="multiplier" class="multiplier">1.00x</p>

        <form action="" method="POST">
            <input type="number" name="bet_amount" class="form-control mb-2" placeholder="Enter bet amount" required>
            <button type="submit" class="btn btn-success w-100">Place Bet</button>
        </form>

        <form action="" method="POST">
            <button type="submit" name="cashout" class="btn-cashout">CASH OUT</button>
        </form>
    <?php endif; ?>
</div>

<script>
// Cooldown Timer
let cooldown = <?= $timeLeft ?>;
if (cooldown > 0) {
    let countdown = setInterval(() => {
        cooldown--;
        document.getElementById("cooldown").textContent = cooldown;
        if (cooldown <= 0) {
            clearInterval(countdown);
            location.reload();
        }
    }, 1000);
}
</script>

</body>
</html>
