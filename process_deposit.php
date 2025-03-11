<?php
session_start();
include('db_connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['amount']) || !isset($input['utr'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = floatval($input['amount']);
$utr = trim($input['utr']);

if ($amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid amount']);
    exit;
}

// Insert deposit request
$sql = "INSERT INTO deposits (user_id, amount, utr, status) VALUES (?, ?, ?, 'pending')";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sds", $user_id, $amount, $utr);
$success = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to process request']);
}
?>
