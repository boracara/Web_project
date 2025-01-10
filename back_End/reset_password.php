<?php
global $conn;

include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Kontrollo nëse token ekziston në databazë
    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Invalid or expired token.");
    }

    // Hash fjalëkalimin e ri
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Përditëso fjalëkalimin dhe fshi token-in
    $stmt = $conn->prepare("UPDATE users SET password = ?, remember_token = NULL WHERE remember_token = ?");
    $stmt->bind_param("ss", $hashed_password, $token);

    if ($stmt->execute()) {
        echo "Password has been reset successfully. <a href='../front_End/login.html'>Log in</a>";
    } else {
        echo "An error occurred. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
