<?php
session_start();
include('db_connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wallet balance
$sql = "SELECT wallet FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $walletBalance);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

echo json_encode(['success' => true, 'wallet' => $walletBalance]);
?>
