<?php
global $conn;
session_start();
include('config.php');

// Kontrollo nëse përdoruesi është i loguar
if (!isset($_SESSION['user_id'])) {
    die("Ju duhet të jeni i loguar për të parë eventet.");
}

$user_id = $_SESSION['user_id'];

// Merr të gjitha eventet
$query = "SELECT e.*, 
                 (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS participants
          FROM events e
          ORDER BY e.event_date ASC";
$result = $conn->query($query);

// Regjistro përdoruesin në një event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    $query = "INSERT INTO event_registrations (event_id, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $event_id, $user_id);

    if ($stmt->execute()) {
        $success_message = "U regjistruat me sukses në event!";
    } else {
        $error_message = "Gabim gjatë regjistrimit: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            margin: 0;
            padding: 20px;
        }

        .events-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        .event {
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
        }

        .event:last-child {
            border-bottom: none;
        }

        .event h2 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }

        .event p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .event .date, .event .participants {
            font-size: 12px;
            color: #999;
        }

        form {
            margin-top: 10px;
        }

        form button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }
    </style>
</head>
<body>
<div class="events-container">
    <h1>Eventet</h1>
    <?php if (isset($success_message)) echo "<p class='message success'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p class='message error'>$error_message</p>"; ?>
    <?php while ($event = $result->fetch_assoc()): ?>
        <div class="event">
            <h2><?= htmlspecialchars($event['title']) ?></h2>
            <p><?= htmlspecialchars($event['description']) ?></p>
            <p class="date">Data: <?= htmlspecialchars($event['event_date']) ?></p>
            <p class="participants">Pjesëmarrës: <?= htmlspecialchars($event['participants']) ?></p>
            <form action="" method="post">
                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                <button type="submit">Regjistrohu</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
