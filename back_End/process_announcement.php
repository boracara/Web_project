<?php
global $conn;
session_start();
include('../back_End/config.php');

// Kontrollo nëse përdoruesi është admin
if ($_SESSION['role'] !== 'admin') {
    die("Vetëm adminët mund të shtojnë njoftime.");
}

// Ruajtja e njoftimit nëse forma është dorëzuar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_email = $_SESSION['email']; // Emaili i adminit nga sesioni
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    // Ruaj njoftimin në databazë
    $query = "INSERT INTO announcements (admin_email, subject, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $admin_email, $subject, $message);

    if ($stmt->execute()) {
        // Ridrejto te faqja me njoftimet
        header("Location: ../back_End/announcements.php");
        exit();
    } else {
        echo "Gabim gjatë shtimit të njoftimit: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shto Njoftim</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            margin-top: 10px;
            display: block;
            color: #333;
        }

        .form-container input, .form-container textarea, .form-container button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-container button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        .form-container a {
            text-decoration: none;
            color: #007bff;
            display: block;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h1>Shto Njoftim</h1>
    <form action="" method="post">
        <label for="admin_email">Email i Adminit:</label>
        <input type="email" id="admin_email" name="admin_email"
               value="<?= htmlspecialchars($_SESSION['email']) ?>" readonly>

        <label for="subject">Subjekti:</label>
        <input type="text" id="subject" name="subject" placeholder="Shkruani subjektin" required>

        <label for="message">Teksti i Njoftimit:</label>
        <textarea id="message" name="message" rows="5" placeholder="Shkruani njoftimin këtu..." required></textarea>

        <button type="submit">Shto Njoftim</button>
    </form>
    <a href="../back_End/announcements.php">Shiko Njoftimet</a>
</div>
</body>
</html>
