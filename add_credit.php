<?php
// add_credit.php
session_start();
require_once 'db_connection.php';
// OPTIONAL: Verify admin privileges here.

$userID = $_GET['user_id'] ?? '';
$creditError = "";
$creditSuccess = "";

// Process form submission.
if (isset($_POST['add_credit'])) {
  $creditAmount = floatval($_POST['credit_amount']);

  // Retrieve current wallet balance.
  $sql = "SELECT wallet FROM users WHERE user_id = '$userID'";
  $result = mysqli_query($conn, $sql);
  $currentWallet = 0;
  if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $currentWallet = floatval($row['wallet']);
  }
  $newWallet = $currentWallet + $creditAmount;
  $updateSql = "UPDATE users SET wallet = '$newWallet' WHERE user_id = '$userID'";
  if (mysqli_query($conn, $updateSql)) {
    $creditSuccess = "Credit added successfully. New wallet balance: ₹" . number_format($newWallet, 2);
  } else {
    $creditError = "Error updating credit: " . mysqli_error($conn);
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Credit - Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
</head>
<body class="bg-light">
  <div class="container py-5" style="max-width: 480px;">
    <h2 class="mb-4 text-center">Add Credit to User: <?php echo htmlspecialchars($userID); ?></h2>
    <?php if (!empty($creditError)): ?>
      <div class="alert alert-danger"><?php echo $creditError; ?></div>
    <?php endif; ?>
    <?php if (!empty($creditSuccess)): ?>
      <div class="alert alert-success"><?php echo $creditSuccess; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="mb-3">
        <label for="credit_amount" class="form-label">Credit Amount (₹)</label>
        <input type="number" step="0.01" class="form-control" id="credit_amount" name="credit_amount" required />
      </div>
      <button type="submit" name="add_credit" class="btn btn-primary w-100">Add Credit</button>
    </form>
    <div class="mt-3 text-center">
      <a href="admin.php" class="text-decoration-none">Back to Admin Panel</a>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
