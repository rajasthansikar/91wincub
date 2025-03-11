<?php
require_once 'db_connection.php'; // Connect to the database

$sql = "SELECT * FROM fire_news ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire News</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .news-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .news-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .news-item img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .news-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<div class="news-container">
    <h2 class="text-center">🔥 Fire News</h2>
    
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="news-item">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><small>Published on: <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></small></p>
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="News Image">
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No news available.</p>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
