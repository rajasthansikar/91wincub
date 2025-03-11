<?php
session_start();
require_once 'db_connection.php';


// Handle user deletion
if (isset($_GET['delete'])) {
    $deleteUserID = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE user_id = '$deleteUserID'");
    header("Location: admin.php");
    exit;
}

// Handle deposit approval/rejection
if (isset($_GET['approve'])) {
    $depositId = $_GET['approve'];
    $depositQuery = mysqli_query($conn, "SELECT user_id, amount FROM deposit_requests WHERE id='$depositId' AND status='Pending'");
    if ($row = mysqli_fetch_assoc($depositQuery)) {
        $userId = $row['user_id'];
        $amount = $row['amount'];

        // Add deposit amount to user's wallet
        mysqli_query($conn, "UPDATE users SET wallet = wallet + $amount WHERE user_id = '$userId'");
        mysqli_query($conn, "UPDATE deposit_requests SET status='Approved' WHERE id='$depositId'");
    }
}
if (isset($_GET['reject'])) {
    $depositId = $_GET['reject'];
    mysqli_query($conn, "UPDATE deposit_requests SET status='Rejected' WHERE id='$depositId'");
}

// Fetch users
$users = mysqli_query($conn, "SELECT * FROM users");

// Fetch deposit requests
$deposits = mysqli_query($conn, "SELECT * FROM deposit_requests ORDER BY created_at DESC");

// Fetch QR Code
$qr_result = mysqli_query($conn, "SELECT qr_image FROM admin_settings LIMIT 1");
$qr_row = mysqli_fetch_assoc($qr_result);
$qr_image = $qr_row ? $qr_row['qr_image'] : 'default_qr.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - 91Win</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-center">🏆 Admin Panel</h1>

        <!-- 🔍 Search Users -->
        <input type="text" id="searchUser" class="form-control mb-3" placeholder="🔍 Search users by ID or Phone..." onkeyup="searchUsers()">

        <!-- 📋 User Management -->
        <h2>User Management</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Phone</th>
                    <th>Wallet</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTable">
                <?php while ($row = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td>₹<span id="wallet_<?= $row['user_id'] ?>"><?= number_format($row['wallet'], 2) ?></span></td>
                        <td>
                            <button class="btn btn-success btn-sm" onclick="updateBalance('<?= $row['user_id'] ?>', 'add')">➕ Add</button>
                            <button class="btn btn-warning btn-sm" onclick="updateBalance('<?= $row['user_id'] ?>', 'deduct')">➖ Deduct</button>
                            <a href="?delete=<?= $row['user_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">🗑️ Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- 🚀 Deposit Requests -->
        <h2 class="mt-4">💰 Deposit Requests</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Amount</th>
                    <th>UTR</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($deposits)): ?>
                    <tr id="deposit_<?= $row['id'] ?>">
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td>₹<?= number_format($row['amount'], 2) ?></td>
                        <td><?= htmlspecialchars($row['utr']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <?php if ($row['status'] === 'Pending'): ?>
                                <button class="btn btn-primary btn-sm" onclick="approveDeposit(<?= $row['id'] ?>, <?= $row['amount'] ?>, '<?= $row['user_id'] ?>')">✅ Approve</button>
                                <button class="btn btn-danger btn-sm" onclick="rejectDeposit(<?= $row['id'] ?>)">❌ Reject</button>
                            <?php else: ?>
                                <span class="badge bg-<?php echo ($row['status'] === 'Approved') ? 'success' : 'danger'; ?>">
                                    <?= $row['status'] ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- 🖼 Upload QR Code -->
        <h3>📷 Update QR Code</h3>
        <img src="uploads/<?= htmlspecialchars($qr_image) ?>" width="200"><br>
        <form action="upload_qr.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="qr_image" class="form-control mb-2" required>
            <button type="submit" class="btn btn-primary">Upload QR</button>
        </form>
    </div>

<script>
// 🔍 Search Users
function searchUsers() {
    let input = document.getElementById("searchUser").value.toLowerCase();
    let rows = document.getElementById("userTable").getElementsByTagName("tr");

    for (let row of rows) {
        let userId = row.cells[0].innerText.toLowerCase();
        let phone = row.cells[1].innerText.toLowerCase();
        row.style.display = userId.includes(input) || phone.includes(input) ? "" : "none";
    }
}

// 💰 Update User Balance (Admin Only)
function updateBalance(userId, action) {
    let amount = prompt(`Enter amount to ${action === 'add' ? 'add' : 'deduct'}:`);
    if (!amount || isNaN(amount) || amount <= 0) {
        alert("Invalid amount!");
        return;
    }

    fetch("update_wallet.php", {
        method: "POST",
        body: JSON.stringify({ user_id: userId, amount: amount, action: action }),
        headers: { "Content-Type": "application/json" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`wallet_${userId}`).innerText = parseFloat(data.wallet).toFixed(2);
            Swal.fire("Success!", `Wallet updated successfully!`, "success");
        } else {
            Swal.fire("Error!", data.error, "error");
        }
    });
}

// ✅ Approve Deposit Request
function approveDeposit(depositId, amount, userId) {
    fetch("approve_deposit.php", {
        method: "POST",
        body: JSON.stringify({ deposit_id: depositId, amount: amount, user_id: userId }),
        headers: { "Content-Type": "application/json" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`deposit_${depositId}`).remove();
            Swal.fire("Deposit Approved!", `₹${amount} added to ${userId}'s wallet.`, "success");
        } else {
            Swal.fire("Error!", data.error, "error");
        }
    });
}


</script>

</body>
</html>
