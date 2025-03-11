<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = "SELECT user_id, wallet FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $user_id, $wallet);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$popup_script = "";

// **Store Last Selected Level in Session**
if (isset($_POST['level'])) {
    $_SESSION['level'] = $_POST['level'];
}
$level = $_SESSION['level'] ?? 'Low';

// **Define Levels**
$levelSettings = [
    'Low' => ['colors' => ['Red', 'Black'], 'multiplier' => 1.5],
    'Medium' => ['colors' => ['Red', 'Black', 'Green', 'Yellow'], 'multiplier' => 2.0],
    'High' => ['colors' => ['Red', 'Black', 'Green', 'Yellow', 'Purple', 'Pink'], 'multiplier' => 3.0]
];

$availableColors = $levelSettings[$level]['colors'];
$multiplier = $levelSettings[$level]['multiplier'];

// **Ensure 'level' column exists in bets table**
mysqli_query($conn, "ALTER TABLE bets ADD COLUMN IF NOT EXISTS level VARCHAR(20) NOT NULL DEFAULT 'Low'");

// **Prevent Duplicate Form Submission (CSRF Token)**
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choice']) && $_POST['token'] === $_SESSION['token']) {
    $amount = floatval($_POST['amount']);
    $choice = $_POST['choice'];

    if ($amount > $wallet) {
        $popup_script = "showToast('Insufficient balance!', 'warning');";
    } else {
        mysqli_query($conn, "UPDATE users SET wallet = wallet - $amount WHERE user_id = '$user_id'");

       // Adjust win probability based on wallet balance
$baseWinChance = 45; // Default win rate
if ($wallet > 5000) {
    $baseWinChance = 35; // Reduce win chance for high balances
} elseif ($wallet > 10000) {
    $baseWinChance = 25; // Harder to win if balance is very high
}

// Adjust for fast betting (if betting within 10 seconds of last bet)
$lastBetTimeQuery = "SELECT created_at FROM bets WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1";
$lastBetTimeResult = mysqli_query($conn, $lastBetTimeQuery);
$lastBetTimeRow = mysqli_fetch_assoc($lastBetTimeResult);

$fastBetPenalty = 0;
if ($lastBetTimeRow) {
    $lastBetTime = strtotime($lastBetTimeRow['created_at']);
    if (time() - $lastBetTime < 10) { // If user bets within 10 seconds
        $fastBetPenalty = 10; // Reduce win chance by 10%
    }
}

// Final adjusted win chance
$finalWinChance = max(10, $baseWinChance - $fastBetPenalty);

$random = mt_rand(1, 100);
if ($random <= $finalWinChance) { 
    $winning_result = $choice;
} else {
    do {
        $winning_result = $availableColors[array_rand($availableColors)];
    } while ($winning_result === $choice);
}


        $status = ($choice === $winning_result) ? 'Win' : 'Lose';
        $payout = ($status === 'Win') ? $amount * $multiplier : 0;

        // Insert bet record
        $insertBet = "INSERT INTO bets (user_id, amount, choice, result, status, level) 
                      VALUES ('$user_id', '$amount', '$choice', '$winning_result', '$status', '$level')";
        mysqli_query($conn, $insertBet);

        if ($status === 'Win') {
            mysqli_query($conn, "UPDATE users SET wallet = wallet + $payout WHERE user_id = '$user_id'");
        }

        $_SESSION['token'] = bin2hex(random_bytes(32)); // Generate new CSRF token

        $popup_script = "saveBet('$choice', '$winning_result', '$status', $amount, '$level'); 
                 showToast('Result: $winning_result - You $status!', '" . ($status === 'Win' ? 'success' : 'error') . "');";

}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Color Prediction Game</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #ffface; font-family: Arial, sans-serif; }
        .game-container { max-width: 400px; margin: 20px auto; background: #fff3cd; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); text-align: center; }
        .header { display: flex; justify-content: space-between; padding: 10px; border-bottom: 2px solid black; background: #ffeb99; }
        .color-options { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin: 20px 0; }
        .color-btn { width: 70px; height: 70px; border-radius: 50%; border: 3px solid black; cursor: pointer; }
        .history-box { background: #ffd966; padding: 10px; border-radius: 10px; text-align: left; margin-top: 20px; }
        .btn-bet { background: green; color: white; font-size: 16px; width: 100%; padding: 10px; border-radius: 10px; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="header">
    <span><strong>👤 <?= $user_id ?></strong></span>
    <span>💰 Wallet: ₹<span id="wallet"><?= number_format($wallet, 2) ?></span></span>
</div>

<div class="container">
    <div class="game-container">
        <form action="" method="POST">
            <h4>🎖 Choose Level</h4>
            <select name="level" class="form-control mb-2 w-75" onchange="this.form.submit()">
                <option value="Low" <?= $level === 'Low' ? 'selected' : '' ?>>Low (2 Colors, 1.5x)</option>
                <option value="Medium" <?= $level === 'Medium' ? 'selected' : '' ?>>Medium (4 Colors, 2x)</option>
                <option value="High" <?= $level === 'High' ? 'selected' : '' ?>>High (6 Colors, 3x)</option>
            </select>
        </form>

        <h4>💰 Enter Bet Amount</h4>
        <form action="" method="POST">
            <input type="hidden" name="level" value="<?= $level ?>">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="number" name="amount" class="form-control mb-2 w-75" placeholder="Enter bet amount" required>

            <div class="color-options">
                <?php foreach ($availableColors as $color): ?>
                    <button type="submit" name="choice" value="<?= $color ?>" class="color-btn" style="background: <?= strtolower($color) ?>;"></button>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-bet">PLACE BET</button>
        </form>

        <div class="history-box">
            <h5>Recent Bets</h5>
            <ul id="bet-history"></ul>
        </div>
    </div>
</div>

<script>
function showToast(message, type) {
    Swal.fire({
        title: message,
        toast: false,
        position: 'center',
        showConfirmButton: true,
        allowOutsideClick: false,
        timer: 6000,
        icon: type
    }).then(() => {
        updateWallet(); // Only update wallet AFTER popup closes
    });
}



function saveBet(choice, result, status, amount, level) {
    let bets = JSON.parse(localStorage.getItem('betHistory')) || [];
    bets.unshift({ choice, result, status, amount, level, date: new Date().toLocaleString() });
    if (bets.length > 5) bets.pop();
    localStorage.setItem('betHistory', JSON.stringify(bets));
    displayBetHistory();
}

function displayBetHistory() {
    let bets = JSON.parse(localStorage.getItem('betHistory')) || [];
    document.getElementById('bet-history').innerHTML = bets.map(bet => `<li>${bet.date} - Level: ${bet.level} | Bet: ${bet.choice} | Result: ${bet.result} | ${bet.status} | ₹${bet.amount}</li>`).join('');
}

function updateWallet() {
    setTimeout(() => {
        fetch("fetch_wallet.php").then(res => res.json()).then(data => {
            if (data.success) {
                document.getElementById("wallet").textContent = data.wallet.toFixed(2);
            }
        });
    }, 6000); // Wait 6 seconds before updating the wallet
}


<?php echo $popup_script; ?>
displayBetHistory();
</script>

</body>
</html>
