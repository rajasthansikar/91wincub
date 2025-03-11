<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $user['password'];

    if (!empty($_FILES['profile_photo']['name'])) {
        $imageName = time() . "_" . $_FILES['profile_photo']['name'];
        $imageTmp = $_FILES['profile_photo']['tmp_name'];
        move_uploaded_file($imageTmp, "uploads/$imageName");
        $profile_photo = $imageName;
    } else {
        $profile_photo = $user['profile_photo'];
    }

    $update_sql = "UPDATE users SET phone='$phone', date_of_birth='$date_of_birth', password='$password', profile_photo='$profile_photo' WHERE user_id='$user_id'";
    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit;
    } else {
        $_SESSION['error'] = "Something went wrong. Try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #121212;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .profile-container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(255, 255, 255, 0.2);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffcc00;
            box-shadow: 0px 0px 10px #ffcc00;
        }
        .form-control {
            background: #222;
            border: none;
            color: #fff;
        }
        .form-control:focus {
            background: #333;
            color: #fff;
        }
        .btn-custom {
            background: #ffcc00;
            color: #000;
            font-weight: bold;
            border: none;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background: #ffdd44;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="profile-container">
        <div class="profile-header">
            <h2>👤 User Profile</h2>
            <img src="uploads/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" class="profile-photo">
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">User ID:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['user_id']); ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone:</label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Date of Birth:</label>
                <input type="date" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">New Password (optional):</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Photo:</label>
                <input type="file" name="profile_photo" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Wallet Balance:</label>
                <input type="text" class="form-control" value="₹<?php echo number_format($user['wallet'], 2); ?>" disabled>
            </div>

            <button type="submit" class="btn btn-custom w-100">Update Profile</button>
        </form>
    </div>

    <div class="text-center mt-3">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
