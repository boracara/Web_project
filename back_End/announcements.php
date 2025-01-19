<?php
global $conn;
include('config.php');

// Merr të gjitha njoftimet
$query = "SELECT a.*, 
                 COALESCE(SUM(CASE WHEN v.vote_type = 'like' THEN 1 ELSE 0 END), 0) AS likes,
                 COALESCE(SUM(CASE WHEN v.vote_type = 'dislike' THEN 1 ELSE 0 END), 0) AS dislikes
          FROM announcements a
          LEFT JOIN votes v ON a.id = v.announcement_id
          GROUP BY a.id
          ORDER BY a.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Njoftimet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            margin: 0;
            padding: 20px;
        }

        .announcements-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .announcement {
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
        }

        .announcement:last-child {
            border-bottom: none;
        }

        .announcement h3 {
            margin: 0;
            color: #007bff;
        }

        .announcement p {
            margin: 5px 0;
            color: #333;
        }

        .announcement .time {
            font-size: 12px;
            color: #999;
        }

        .vote-buttons {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        .vote-buttons form button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .vote-buttons .like {
            background-color: #28a745;
            color: white;
        }

        .vote-buttons .like:hover {
            background-color: #218838;
        }

        .vote-buttons .dislike {
            background-color: #dc3545;
            color: white;
        }

        .vote-buttons .dislike:hover {
            background-color: #c82333;
        }

        .results {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }

        a.button {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            font-weight: bold;
        }

        a.button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="announcements-container">
    <h1>Njoftimet</h1>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="announcement">
            <h3><?= htmlspecialchars($row['subject']) ?></h3>
            <p><?= htmlspecialchars($row['message']) ?></p>
            <p class="time">Nga: <?= htmlspecialchars($row['admin_email']) ?> | <?= htmlspecialchars($row['created_at']) ?></p>

            <div class="vote-buttons">
                <form action="vote.php" method="post">
                    <input type="hidden" name="announcement_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="vote_type" value="like" class="like">👍 Like</button>
                    <button type="submit" name="vote_type" value="dislike" class="dislike">👎 Dislike</button>
                </form>
            </div>
            <div class="results">
                <p>👍 Likes: <?= $row['likes'] ?> | 👎 Dislikes: <?= $row['dislikes'] ?></p>
            </div>
        </div>
    <?php endwhile; ?>
    <a href="../back_End/admin_dashboard.php" class="button">Kthehu te Profili</a>
</div>
</body>
</html>
