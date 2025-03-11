<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// Check if admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get JSON request data
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['deposit_id']) || !isset($input['amount']) || !isset($input['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$deposit_id = intval($input['deposit_id']);
$amount = floatval($input['amount']);
$user_id = $input['user_id'];

// Check if deposit exists & is pending
$sql = "SELECT * FROM deposit_requests WHERE id = ? AND status = 'Pending'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $deposit_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$deposit = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$deposit) {
    echo json_encode(['success' => false, 'error' => 'Deposit not found or already processed']);
    exit;
}

// Update user's wallet balance
mysqli_query($conn, "UPDATE users SET wallet = wallet + $amount WHERE user_id = '$user_id'");

// Mark deposit as approved
mysqli_query($conn, "UPDATE deposit_requests SET status = 'Approved' WHERE id = $deposit_id");

echo json_encode(['success' => true, 'message' => 'Deposit approved successfully']);
?>
