<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Përfshi skedarët e PHPMailer
require 'vendor/autoload.php'; // Nëse përdor Composer

$mail = new PHPMailer(true);

try {
    // Konfigurimi i serverit SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587; // Përdor 465 nëse ke aktivizuar SSL
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Përdor 'ssl' nëse porta është 465
    $mail->SMTPAuth = true;
    $mail->Username = 'platformaweb123@gmail.com'; // Vendos email-in tënd
    $mail->Password = 'platforma111.'; // Vendos fjalëkalimin

    // Vendos detajet e email-it
    $mail->setFrom('email_yne@gmail.com', 'Emri Yt');
    $mail->addAddress('marrësi@example.com', 'Marrësi'); // Vendos adresën e marrësit

    $mail->isHTML(true);
    $mail->Subject = 'Subjekti i email-it';
    $mail->Body    = 'Ky është përmbajtja e email-it në HTML.';
    $mail->AltBody = 'Ky është përmbajtja e thjeshtë e email-it.';

    // Dërgo email-in
    $mail->send();
    echo 'Mesazhi u dërgua me sukses!';
} catch (Exception $e) {
    echo "Mesazhi nuk u dërgua. Gabimi: {$mail->ErrorInfo}";
}
?>
