<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount']) && isset($_POST['utr'])) {
    $user_id = $_SESSION['user_id'];
    $amount = floatval($_POST['amount']);
    $utr = mysqli_real_escape_string($conn, $_POST['utr']);

    mysqli_query($conn, "INSERT INTO deposit_requests (user_id, amount, utr, status) VALUES ('$user_id', '$amount', '$utr', 'Pending')");

    echo "<script>alert('Your deposit request has been submitted!'); window.location.href='wallet.php';</script>";
    exit;
}

// Fetch QR Code from Database
$qrResult = mysqli_query($conn, "SELECT qr_image FROM admin_settings WHERE id = 1");
$qrRow = mysqli_fetch_assoc($qrResult);
$qrCodePath = isset($qrRow['qr_image']) ? "uploads/" . $qrRow['qr_image'] : "uploads/qr_code.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Money</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .deposit-container {
            max-width: 450px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .qr-code {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            border: 2px solid #ddd;
            border-radius: 10px;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="deposit-container text-center w-100">
        <a href="wallet.php" class="btn-back">⬅ Back to Wallet</a>
        <h2 class="mb-3">Deposit Money</h2>

        <!-- Responsive QR Code Image with Live Update -->
        <img id="qrImage" src="<?= $qrCodePath ?>" alt="QR Code" class="qr-code img-fluid mb-3">

        <p class="text-muted">Scan the QR code and enter your UTR number.</p>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Amount to Deposit (₹):</label>
                <input type="number" name="amount" class="form-control" placeholder="Enter amount" required>
            </div>
            <div class="mb-3">
                <label class="form-label">UTR Number:</label>
                <input type="text" name="utr" class="form-control" placeholder="Enter UTR number" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Submit Deposit Request</button>
        </form>
    </div>
</div>

<!-- Live QR Code Update -->
<script>
    setInterval(() => {
        document.getElementById('qrImage').src = "<?= $qrCodePath ?>?" + new Date().getTime();
    }, 5000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
