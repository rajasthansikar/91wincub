<?php
// mines_game.php
session_start();
require_once 'db_connection.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's current wallet from the database
$sql = "SELECT wallet FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $wallet = floatval($row['wallet']);
} else {
    $wallet = 0.00;
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['play'])) {
    $bet = floatval($_POST['bet']);
    $chosenCell = intval($_POST['cell']); // expecting a value between 1 and 9
    if ($bet <= 0) {
        $message = "Bet must be greater than 0.";
    } elseif ($bet > $wallet) {
        $message = "Insufficient funds for that bet.";
    } elseif ($chosenCell < 1 || $chosenCell > 9) {
        $message = "Invalid cell selection. Choose a number between 1 and 9.";
    } else {
        // Randomly designate one cell (1 to 9) as the mine
        $mineCell = mt_rand(1, 9);
        if ($chosenCell == $mineCell) {
            // Loss: Player hit the mine
            $wallet -= $bet;
            $message = "Boom! You hit the mine at cell $mineCell. You lost ₹" . number_format($bet, 2) . ".";
        } else {
            // Win: Player avoids the mine; win multiplier 2x
            $winnings = $bet * 2;
            $wallet += $winnings;
            $message = "Phew! You avoided the mine. The mine was at cell $mineCell. You win ₹" . number_format($winnings, 2) . ".";
        }
        // Update wallet in the database
        $update_sql = "UPDATE users SET wallet = '$wallet' WHERE user_id = '$user_id'";
        mysqli_query($conn, $update_sql);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mines Game</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 480px;">
        <h1 class="text-center mb-4">Mines Game</h1>
        <p class="text-center">Wallet Balance: ₹<?php echo number_format($wallet, 2); ?></p>
        <?php if ($message !== ""): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($wallet > 0): ?>
            <form method="post" action="">
                <div class="mb-3">
                    <label for="bet" class="form-label">Enter your bet amount (₹):</label>
                    <input type="number" step="0.01" name="bet" id="bet" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="cell" class="form-label">Choose a cell (1-9):</label>
                    <input type="number" name="cell" id="cell" class="form-control" min="1" max="9" required>
                </div>
                <div class="d-grid">
                    <input type="submit" name="play" value="Play Mines Game" class="btn btn-primary">
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger text-center">Game over! Your wallet is empty.</div>
        <?php endif; ?>

        <div class="mt-3 text-center">
            <a href="dashboard.php" class="text-decoration-none">Back to Dashboard</a>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
