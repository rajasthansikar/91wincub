<?php
// reset_password.php
session_start();
require_once 'db_connection.php';

$resetError = "";
$resetSuccess = "";

if (isset($_POST['reset_pass'])) {
  $userID = $_POST['user_id'] ?? '';
  $dob = $_POST['dob'] ?? '';
  $phone = $_POST['phone'] ?? '';
  $newPass = $_POST['new_password'] ?? '';
  $confirmNewPass = $_POST['confirm_new_password'] ?? '';

  if ($newPass !== $confirmNewPass) {
    $resetError = "Passwords do not match!";
  } else {
    $sql = "SELECT * FROM users WHERE user_id = '$userID' AND date_of_birth = '$dob' AND phone = '$phone'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
      $updateSql = "UPDATE users SET password = '$newPass' WHERE user_id = '$userID'";
      if (mysqli_query($conn, $updateSql)) {
        $resetSuccess = "Password reset successfully. You can now log in.";
      } else {
        $resetError = "Error updating password: " . mysqli_error($conn);
      }
    } else {
      $resetError = "User data not found or doesn't match our records!";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - Daman Club</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
</head>
<body class="bg-light">
  <div class="container py-5" style="max-width: 500px;">
    <h2 class="mb-4 text-center">Reset Password</h2>
    <?php if (!empty($resetError)): ?>
      <div class="alert alert-danger"><?php echo $resetError; ?></div>
    <?php endif; ?>
    <?php if (!empty($resetSuccess)): ?>
      <div class="alert alert-success"><?php echo $resetSuccess; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="mb-3">
        <label for="user_id" class="form-label">User ID</label>
        <input type="text" class="form-control" id="user_id" name="user_id" required/>
      </div>
      <div class="mb-3">
        <label for="dob" class="form-label">Date of Birth</label>
        <input type="date" class="form-control" id="dob" name="dob" required/>
      </div>
      <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="tel" class="form-control" id="phone" name="phone" required/>
      </div>
      <div class="mb-3">
        <label for="new_password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="new_password" name="new_password" required/>
      </div>
      <div class="mb-3">
        <label for="confirm_new_password" class="form-label">Confirm New Password</label>
        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required/>
      </div>
      <button type="submit" class="btn btn-primary w-100" name="reset_pass">Reset Password</button>
    </form>
    <div class="mt-3 text-center">
      <a href="index.php" class="text-decoration-none">Back to Login</a>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
