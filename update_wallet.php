<?php
session_start();
include('db_connection.php');

header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['user_id']) && !isset($_SESSION['is_admin'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['amount']) || !isset($input['action']) || !is_numeric($input['amount'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request data']);
    exit;
}

$amount = floatval($input['amount']);
$action = $input['action'];

// Admin can update any user's wallet
if (isset($input['user_id']) && $_SESSION['is_admin']) {
    $user_id = $input['user_id'];
} else {
    $user_id = $_SESSION['user_id'];
}

// Fetch current wallet balance
$sql = "SELECT wallet FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $wallet);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if ($wallet === null) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}

// Determine new wallet balance
if ($action === 'add') {
    $newWallet = $wallet + $amount;
} elseif ($action === 'deduct') {
    if ($amount > $wallet) {
        echo json_encode(['success' => false, 'error' => 'Insufficient balance']);
        exit;
    }
    $newWallet = $wallet - $amount;
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

// Update wallet balance
$sql = "UPDATE users SET wallet = ? WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ds", $newWallet, $user_id);
$success = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Return response
if ($success) {
    echo json_encode(['success' => true, 'wallet' => number_format($newWallet, 2)]);
} else {
    echo json_encode(['success' => false, 'error' => 'Wallet update failed']);
}

mysqli_close($conn);
?>
