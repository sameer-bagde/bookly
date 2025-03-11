<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'bagdesameer92@gmail.com';                     //SMTP username
    $mail->Password   = 'vxfc qvew spxm girh';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('bagdesameer92@gmail.com', 'Bookly Support');
    $mail->addAddress('sameer.bagde.cse@ghrce.raisoni.net', 'Joe User');     //Add a recipient


    $mail->isHTML(true);
    $mail->Subject = 'Welcome to Bookly Store!';
    $mail->Body    = '<h2>Registration Successful</h2><p>Dear User,</p><p>Thank you for registering with <b>Bookly Store</b>. We are excited to have you!</p>';
    $mail->AltBody = 'Dear User, Thank you for registering with Bookly Store. We are excited to have you!';

    if ($mail->send()) {
        echo 'Message has been sent successfully.';
    } else {
        echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}