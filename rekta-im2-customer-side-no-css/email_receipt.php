<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once 'config.php';
session_start();

if (!isset($_POST['trans_header_id']) || !isset($_SESSION['user_id'])) {
    exit('Invalid request');
}

$user_id = $_SESSION['user_id'];
$trans_header_id = (int)$_POST['trans_header_id'];

// Get email
$stmt = $mysqli->prepare("SELECT email FROM user_login WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

// Get receipt details (simplified for this demo)
$receipt_body = "Thanks for your order!\n\n(Order details go here)";

// Setup PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';        // Gmail SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com'; // Your Gmail
    $mail->Password = 'your_app_password';    // Use Gmail App Password (not your login)
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your_email@gmail.com', 'Rekta Shop');
    $mail->addAddress($email);
    $mail->Subject = "Your Order Receipt #$trans_header_id";
    $mail->Body    = $receipt_body;

    $mail->send();
    echo "<script>alert('✅ Receipt sent to your email.'); window.history.back();</script>";
} catch (Exception $e) {
    echo "<script>alert('❌ Email could not be sent. Error: {$mail->ErrorInfo}'); window.history.back();</script>";
}
?>
