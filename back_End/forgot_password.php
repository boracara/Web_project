<?php
global $conn;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $reset_link = "http://localhost/Web_project/front_End/reset_password.html?token=$token";



        // Ruaj token në databazë
        $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Dërgo email me PHPMailer
        $mail = new PHPMailer(true); // Inicializo PHPMailer

        try {
            // Konfigurimi i SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'platformaweb123@gmail.com'; // Gmail-i yt
            $mail->Password = 'ueqe xvuz mgse wwsz';   // App Password ose fjalëkalimi
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Informacioni i email-it
            $mail->setFrom('your_email@gmail.com', 'Your Website');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset';
            $mail->Body = "Click the link to reset your password: $reset_link";

            $mail->send();
            echo "Password reset link sent to your email.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found.";
    }

    $stmt->close();
    $conn->close();
}

$mail->SMTPDebug = 3;
$mail->Debugoutput = 'html';

?>
