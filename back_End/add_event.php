<?php
global $conn;
session_start();
include('config.php');

// Kontrollo nëse përdoruesi është admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Vetëm adminët mund të shtojnë evente.");
}

// Shto një event të ri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $event_date = $conn->real_escape_string($_POST['event_date']);

    $query = "INSERT INTO events (title, description, event_date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $title, $description, $event_date);

    if ($stmt->execute()) {
        $success_message = "Eventi u shtua me sukses!";
    } else {
        $error_message = "Gabim gjatë shtimit të eventit: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shto Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffdc67;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .form-container label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            color: #333;
        }

        .form-container input,
        .form-container textarea,
        .form-container button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-container textarea {
            resize: none;
        }

        .form-container button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        .form-container a {
            text-decoration: none;
            color: #007bff;
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .form-container a:hover {
            color: #0056b3;
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
<div class="form-container">
    <h1>Shto Event</h1>
    <?php if (isset($success_message)) echo "<p class='message success'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p class='message error'>$error_message</p>"; ?>
    <form action="" method="post">
        <label for="title">Titulli:</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Përshkrimi:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="event_date">Data e Eventit:</label>
        <input type="date" id="event_date" name="event_date" required>

        <button type="submit">Shto Event</button>
    </form>
    <a href="admin_dashboard.php">Kthehu te Paneli</a>
</div>
</body>
</html>
