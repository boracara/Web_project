<?php
global $conn;
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $verification_code = $_POST['verification_code'];

    // Kontrollo nëse kodi i verifikimit është i saktë
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ?");
    $stmt->bind_param("ss", $email, $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Përditëso statusin e verifikimit
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            echo "Your email has been successfully verified! You can now log in.";
            header("Location: /Web_project/front_End/login.html");
            exit();
        } else {
            echo "Failed to update verification status.";
        }
    } else {
        echo "Invalid verification code or email.";
    }

    $stmt->close();
    $conn->close();
}
?>
