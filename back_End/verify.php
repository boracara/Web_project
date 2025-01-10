<?php
global $conn;
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $verification_code = $_POST['verification_code'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ?");
    $stmt->bind_param("ss", $email, $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $update_query = "UPDATE users SET is_verified = 1 WHERE email = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        echo "Your email has been successfully verified!";
    } else {
        echo "Invalid verification code.";
    }

    $stmt->close();
    $conn->close();
}
?>
