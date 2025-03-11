<?php
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
    $chosenColor = $_POST['color'];

    // Validate bet amount
    if ($bet <= 0) {
        $message = "Bet must be greater than 0.";
    } elseif ($bet > $wallet) {
        $message = "Insufficient funds for that bet.";
    } else {
        // Generate outcome using weighted probabilities:
        // 45% chance for Red, 45% for Green, 10% for Violet
        $rand = mt_rand() / mt_getrandmax(); // random float between 0 and 1
        if ($rand < 0.45) {
            $outcome = "Red";
        } elseif ($rand < 0.90) {
            $outcome = "Green";
        } else {
            $outcome = "Violet";
        }

        // Define payoff multipliers
        $multipliers = [
            "Red"    => 1.0,
            "Green"  => 1.0,
            "Violet" => 5.0
        ];

        // Determine result and update wallet accordingly
        if ($chosenColor === $outcome) {
            $winnings = $bet * $multipliers[$chosenColor];
            $wallet += $winnings;
            $message = "Congratulations! The outcome is $outcome. You won ₹" . number_format($winnings, 2) . ".";
        } else {
            $wallet -= $bet;
            $message = "Sorry, the outcome is $outcome. You lost your bet of ₹" . number_format($bet, 2) . ".";
        }
        // Update the wallet in the database
        $update_sql = "UPDATE users SET wallet = '$wallet' WHERE user_id = '$user_id'";
        mysqli_query($conn, $update_sql);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Colour Prediction Game</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 480px;">
        <h1 class="text-center mb-4">Colour Prediction Game</h1>
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
                    <label for="color" class="form-label">Choose a color:</label>
                    <select name="color" id="color" class="form-select">
                        <option value="Red">Red</option>
                        <option value="Green">Green</option>
                        <option value="Violet">Violet</option>
                    </select>
                </div>
                <div class="d-grid">
                    <input type="submit" name="play" value="Play" class="btn btn-primary">
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger text-center">Game over! Your wallet is empty.</div>
        <?php endif; ?>

        <!-- Optionally, provide a link back to dashboard or account page -->
        <div class="mt-3 text-center">
            <a href="dashboard.php" class="text-decoration-none">Back to Dashboard</a>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
