<?php
// Lidhja me databazën


global $pdo, $conn;

include('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sender_email = $_SESSION['email']; // Emaili i përdoruesit që dërgon mesazhin
    $receiver_email = $_POST['receiver_email']; // Emaili i marrësit
    $message = $_POST['message']; // Mesazhi nga forma

    // Validimi i emailit të marrësit
    if (!filter_var($receiver_email, FILTER_VALIDATE_EMAIL)) {
        die("Emaili i marrësit është i pavlefshëm.");
    }

    // Sigurohu që mesazhi nuk është bosh
    if (empty(trim($message))) {
        die("Mesazhi nuk mund të jetë bosh.");
    }

    // Ruaj mesazhin në databazë
    $insert_query = "INSERT INTO messages (sender_email, receiver_email, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sss", $sender_email, $receiver_email, $message);

    if ($stmt->execute()) {
        echo "<script>
            alert('Mesazhi u dërgua me sukses!');
            window.location.href = '/back_End/messages.php?receiver_email=" . urlencode($receiver_email) . "';
        </script>";
    } else {
        echo "Gabim gjatë dërgimit të mesazhit: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
