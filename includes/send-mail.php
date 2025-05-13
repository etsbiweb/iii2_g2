<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require ''.$_SERVER['DOCUMENT_ROOT'].'/iii2_g2/vendor/autoload.php';

class Mail{
    public $mail;
    public function __construct(){
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'jasminpoprzenovic@gmail.com';
        $this->mail->Password = 'cvyi flfi gsyv abnn ';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Port = 465;
    }
    public function SendMail($useremail,$sitemail,$subject,$body){
        try {
            $this->mail->setFrom($sitemail, 'Mailer');
            $this->mail->addAddress($useremail, 'Receiver Name');
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->send();
            return true; // Success
        } 
        catch (Exception $e) {
            // Log or display the error
            error_log("Mail error: {$this->mail->ErrorInfo}");
            throw new Exception("Failed to send email: {$this->mail->ErrorInfo}");
        }
    }

};