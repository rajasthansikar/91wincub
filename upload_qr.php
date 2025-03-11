<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['qr_image'])) {
    $uploadDir = "uploads/";
    $uploadFile = $uploadDir . "qr_code.png"; 

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['qr_image']['tmp_name'], $uploadFile)) {
        mysqli_query($conn, "UPDATE admin_settings SET qr_image = 'qr_code.png' WHERE id = 1");
        echo "<script>alert('QR Code uploaded successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Upload failed. Try again.'); window.location.href='admin.php';</script>";
    }
}
?>
