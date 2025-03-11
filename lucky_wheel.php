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

// Define Wheel Segments (Multipliers)
$wheel_segments = [
    0.5, 0.5, 1.0, 1.0, 2.0, 2.0, 3.0, 3.0, 
    5.0, 5.0, 8.0, 10.0, 20.0 // Jackpot (Rare)
];

$popup_script = "";

// If user spins the wheel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bet_amount'])) {
    $bet_amount = floatval($_POST['bet_amount']);

    if ($bet_amount > $wallet) {
        $popup_script = "showToast('Insufficient Balance!', 'error');";
    } else {
        mysqli_query($conn, "UPDATE users SET wallet = wallet - $bet_amount WHERE user_id = '$user_id'");

        $randomIndex = array_rand($wheel_segments);
        $multiplier = $wheel_segments[$randomIndex];
        $winnings = $bet_amount * $multiplier;

        mysqli_query($conn, "UPDATE users SET wallet = wallet + $winnings WHERE user_id = '$user_id'");

        // Save bet in DB
        $stmt = mysqli_prepare($conn, "INSERT INTO wheel_bets (user_id, amount, multiplier, winnings) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sddd", $user_id, $bet_amount, $multiplier, $winnings);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $popup_script = "spinWheel($randomIndex, $multiplier, $winnings);";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lucky Wheel Spin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <style>
        body { background-color: #121212; font-family: Arial, sans-serif; color: white; text-align: center; }
        .container { max-width: 400px; margin: 30px auto; background: #1e1e1e; padding: 20px; border-radius: 10px; }

        /* CSS Wheel */
        .wheel-container { position: relative; width: 300px; height: 300px; margin: auto; }
        .wheel { width: 100%; height: 100%; border-radius: 50%; position: absolute; transition: transform 5s cubic-bezier(0.17, 0.67, 0.83, 0.67); }
        .arrow { position: absolute; top: -20px; left: 50%; transform: translateX(-50%); font-size: 24px; color: red; }

        .wheel:before { content: ""; width: 100%; height: 100%; border-radius: 50%; background: conic-gradient(
            #f00 0deg 30deg, #ff9800 30deg 60deg, #ff0 60deg 90deg, #0f0 90deg 120deg,
            #00f 120deg 150deg, #800080 150deg 180deg, #f00 180deg 210deg, #ff9800 210deg 240deg,
            #ff0 240deg 270deg, #0f0 270deg 300deg, #00f 300deg 330deg, #800080 330deg 360deg);
            position: absolute;
        }
        
        .spin-btn { background: #ffcc00; color: black; font-size: 18px; padding: 10px; width: 100%; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h2>🎡 Lucky Wheel Spin</h2>
    <p>💰 Wallet: ₹<span id="wallet"><?= number_format($wallet, 2) ?></span></p>

    <div class="wheel-container">
        <div class="arrow">🔻</div>
        <div id="wheel" class="wheel"></div>
    </div>

    <form id="spinForm" action="" method="POST">
        <input type="number" name="bet_amount" id="bet_amount" class="form-control mb-2" placeholder="Enter Bet Amount" required>
        <button type="submit" class="btn spin-btn">SPIN NOW</button>
    </form>
</div>

<script>
function showToast(message, type) {
    Swal.fire({ title: message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, icon: type });
}

function spinWheel(segmentIndex, multiplier, winnings) {
    let wheel = document.getElementById("wheel");
    let segments = 12; // Number of segments
    let spinAngle = 3600 + (360 / segments) * segmentIndex;
    
    wheel.style.transform = `rotate(${spinAngle}deg)`;

    setTimeout(() => {
        Swal.fire(`🎉 Multiplier: ${multiplier}x`, `You won ₹${winnings.toFixed(2)}!`, "success");
        setTimeout(() => { location.reload(); }, 2000);
    }, 5000);
}

$("#spinForm").submit(function(event) {
    event.preventDefault();
    let betAmount = parseFloat($("#bet_amount").val());

    if (isNaN(betAmount) || betAmount <= 0) {
        showToast("Enter a valid bet amount!", "error");
        return;
    }

    $.post("", { bet_amount: betAmount }, function() {
        $("#wheel").css("transform", "rotate(0deg)");
    });
});

<?php echo $popup_script; ?>
</script>

</body>
</html>
