<?php
global $conn;
session_start();
require 'config.php';

// Kontrollo sesionin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_End/login.html");
    exit();
}

// Kontrollo nëse përdoruesi është admin
if ($_SESSION['role'] !== 'admin') {
    die("Nuk keni akses për këtë faqe.");
}

// Merr ID-në nga URL-ja
$requested_user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($requested_user_id === 0) {
    die("ID e pavlefshme.");
}

// Merr të dhënat nga baza e të dhënave
$query = "
    SELECT u.first_name, u.last_name, u.email, u.birthdate, u.gender, u.profile_picture, 
           COALESCE(p.profession, '') AS profession, 
           COALESCE(p.description, '') AS description 
    FROM users u 
    LEFT JOIN user_profiles p ON u.id = p.user_id 
    WHERE u.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $requested_user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("Të dhënat e përdoruesit nuk u gjetën.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shiko Profilin</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .profile-container {
            width: 90%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .profile-picture img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid #007bff;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .profile-details h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .profile-details p {
            font-size: 16px;
            color: #555;
            margin: 10px 0;
            line-height: 1.5;
        }

        .profile-details strong {
            color: #000;
        }

        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="profile-container">
    <div class="profile-picture">
        <img src="<?= htmlspecialchars($user['profile_picture'] ? $user['profile_picture'] : '../uploads/default_profile.png') ?>" alt="Foto e Profilit">
    </div>
    <div class="profile-details">
        <h2>Profili i Përdoruesit</h2>
        <p><strong>First Name:</strong> <?= htmlspecialchars($user['first_name']) ?></p>
        <p><strong>Last Name:</strong> <?= htmlspecialchars($user['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Birthdate:</strong> <?= htmlspecialchars($user['birthdate']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars(ucfirst($user['gender'])) ?></p>
        <p><strong>Employment Field:</strong> <?= htmlspecialchars($user['profession']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($user['description']) ?></p>
        <a href="../back_End/admin_dashboard.php" class="button">Kthehu te Admin Dashboard</a>
    </div>
</div>
</body>
</html>
