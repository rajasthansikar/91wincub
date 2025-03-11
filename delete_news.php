<?php
require_once 'db_connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM fire_news WHERE id = $id";
    mysqli_query($conn, $sql);
}
header("Location: admin_news.php");
exit;
?>
