<?php
// index.php
session_start();
require_once 'db_connection.php';

// Redirect if already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  header("Location: dashboard.php");
  exit;
}

$loginError = "";
$registerError = "";
$registerSuccess = "";

// Handle Login Submission
if (isset($_POST['login'])) {
  $userID = $_POST['user_id'] ?? '';
  $password = $_POST['password'] ?? '';

  // Example: use prepared statements & password hashing in real code.
  $sql = "SELECT * FROM users WHERE user_id = '$userID' AND password = '$password'";
  $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) === 1) {
    $_SESSION['loggedin'] = true;
    $_SESSION['user_id'] = $userID;
    header("Location: dashboard.php");
    exit;
  } else {
    $loginError = "Wrong password or user not found!";
  }
}

// Handle Register Submission
if (isset($_POST['register'])) {
  $newUserID = $_POST['new_user_id'] ?? '';
  $dob = $_POST['dob'] ?? '';
  $phone = $_POST['phone'] ?? '';
  $password = $_POST['reg_password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';

  if ($password !== $confirmPassword) {
    $registerError = "Passwords do not match!";
  } else {
    $checkSql = "SELECT * FROM users WHERE user_id = '$newUserID'";
    $checkResult = mysqli_query($conn, $checkSql);

    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
      $registerError = "UserID already exists. Choose a different one.";
    } else {
      $insertSql = "INSERT INTO users (user_id, date_of_birth, phone, password)
                    VALUES ('$newUserID', '$dob', '$phone', '$password')";
      if (mysqli_query($conn, $insertSql)) {
        $registerSuccess = "Account created successfully! You can now log in.";
      } else {
        $registerError = "Registration failed: " . mysqli_error($conn);
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login / Register - Daman Club</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
</head>
<body class="bg-light">
  <div class="container py-5" style="max-width: 480px;">
    <h1 class="text-center mb-4">Welcome to Daman Club</h1>
    
    <!-- Alert Messages -->
    <?php if (!empty($loginError)): ?>
      <div class="alert alert-danger"><?php echo $loginError; ?></div>
    <?php endif; ?>
    <?php if (!empty($registerError)): ?>
      <div class="alert alert-danger"><?php echo $registerError; ?></div>
    <?php endif; ?>
    <?php if (!empty($registerSuccess)): ?>
      <div class="alert alert-success"><?php echo $registerSuccess; ?></div>
    <?php endif; ?>

    <!-- Tabs for Login and Register -->
    <ul class="nav nav-tabs" id="authTab" role="tablist">
      <li class="nav-item">
        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Register</button>
      </li>
    </ul>
    <div class="tab-content" id="authTabContent">
      <!-- Login Form -->
      <div class="tab-pane fade show active p-3 border border-top-0" id="login" role="tabpanel">
        <form method="POST" action="">
          <div class="mb-3">
            <label for="user_id" class="form-label">User ID</label>
            <input type="text" class="form-control" id="user_id" name="user_id" required/>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required/>
          </div>
          <button type="submit" class="btn btn-primary w-100" name="login">Log In</button>
        </form>
        <div class="mt-3 text-center">
          <a href="reset_password.php" class="text-decoration-none">Forgot/Reset Password?</a>
        </div>
      </div>
      <!-- Register Form -->
      <div class="tab-pane fade p-3 border border-top-0" id="register" role="tabpanel">
        <form method="POST" action="">
          <div class="mb-3">
            <label for="new_user_id" class="form-label">User ID</label>
            <input type="text" class="form-control" id="new_user_id" name="new_user_id" required/>
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
            <label for="reg_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="reg_password" name="reg_password" required/>
          </div>
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required/>
          </div>
          <button type="submit" class="btn btn-success w-100" name="register">Register</button>
        </form>
      </div>
    </div>
  </div>
 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
