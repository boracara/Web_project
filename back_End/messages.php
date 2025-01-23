 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesazhet e Mia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0055f3;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        h1 {
            color: #ffffff;
            margin-bottom: 20px;
        }

        .messages-container {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .message {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .message:last-child {
            border-bottom: none;
        }

        .message p {
            margin: 5px 0;
        }

        .buttons {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }

        .buttons a {
            text-decoration: none;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .buttons a:hover {
            background-color: #0056b3;
        }

        .buttons a.secondary {
            background-color: #6c757d;
        }

        .buttons a.secondary:hover {
            background-color: #5a6268;
        }

        .no-messages {
            text-align: center;
            color: #555;
            font-style: italic;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="buttons">
    <a href="../front_End/sendmessage.html">Dërgo Mesazh</a>
    <a href="../back_End/user_dashboard.php">Kthehu te Profili</a>
</div>

<div class="messages-container">
    <?php
    session_start();
    require 'config.php';
    global $conn;

    try {
        // Kontrollo nëse përdoruesi është loguar
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../front_End/login.html");
            exit();
        }

        // Merr emailin e përdoruesit të loguar
        $user_email = $_SESSION['email'] ?? null;

        if (!$user_email) {
            echo "<script>
            alert('Emaili i përdoruesit nuk është i vendosur. Ju lutemi logohuni përsëri.');
            window.location.href = '../front_End/login.html';
        </script>";
            exit();
        }

        // Merr emailin e marrësit nga URL ose përdor emailin e vetë përdoruesit si parazgjedhje
        $receiver_email = $_GET['receiver_email'] ?? $user_email;

        // Kontrollo nëse `receiver_email` është i vlefshëm
        if (!$receiver_email) {
            echo "<script>
            alert('Emaili i marrësit nuk është i specifikuar.');
            window.location.href = '../back_End/user_dashboard.php';
        </script>";
            exit();
        }

        // Merr mesazhet nga baza e të dhënave
        $stmt = $conn->prepare("
        SELECT sender_email, receiver_email, message, timestamp 
        FROM messages 
        WHERE (sender_email = ? AND receiver_email = ?) 
           OR (sender_email = ? AND receiver_email = ?)
        ORDER BY timestamp ASC
    ");
        $stmt->bind_param("ssss", $user_email, $receiver_email, $receiver_email, $user_email);
        $stmt->execute();
        $messages = $stmt->get_result();

        // Shfaq mesazhet
        if ($messages->num_rows > 0) {
            echo '<h1 style="color: black;">Mesazhet e mia:</h1>';
            while ($msg = $messages->fetch_assoc()) {
                echo "<div class='message'>
                <p><strong>From:</strong> " . htmlspecialchars($msg['sender_email']) . "</p>
                <p><strong>To:</strong> " . htmlspecialchars($msg['receiver_email']) . "</p>
                <p><strong>Message:</strong> " . htmlspecialchars($msg['message']) . "</p>
                <p><strong>Sent At:</strong> " . htmlspecialchars($msg['timestamp']) . "</p>
            </div>";
            }
        } else {
            echo "<div class='no-messages'>Nuk ka mesazhe për t'u shfaqur.</div>";
        }
    } catch (Exception $e) {
        echo "<div class='no-messages'>Gabim gjatë shfaqjes së mesazheve: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    ?>

</div>
</body>
</html>
