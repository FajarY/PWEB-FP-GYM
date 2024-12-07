<?php

namespace University\GymJournal\Backend\App;

use PHPMailer\PHPMailer\PHPMailer;
use University\GymJournal\Backend\App\Logger;

class Mailer
{
    public static function sendOTP($email, $otp) : bool
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_SERVER['GMAIL'];
        $mail->Password = $_SERVER['GMAILMAILER_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($_SERVER['GMAIL'], 'Gym Journal');
        $mail->addAddress($email);
        
        $mail->isHTML(false);

        $mail->Subject = 'Gym Journal Register OTP';
        $mail->Body = 'To verify your registration, please input the code '.$otp;

        $succeed = false;
        if(!$mail->send())
        {
            Logger::Error('Error sending email to '.$email.', Error : '.$mail->ErrorInfo);
        }
        else
        {
            $succeed = true;
        }

        $mail->smtpClose();
        return $succeed;
    }
}
?>