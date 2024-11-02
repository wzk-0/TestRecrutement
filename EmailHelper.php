<?php
namespace TestRecrutement\SuiviColis;
require 'lib/PHPMailer/src/Exception.php';
require 'lib/PHPMailer/src/PHPMailer.php';
require 'lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class EmailHelper{
    
    private $mailer;

    function __construct(){
        $this->mailer = new PHPMailer(true);//instance of PHPMailer | true to enable exception
        $this->initMailer();
    }

    /**
     * Initialization of the PHPMailer
    */ 
    private function initMailer(){
        $this->mailer->isSMTP();                                      //Send using SMTP
        $this->mailer->Host = EMAIL_HOST;                             //Set the SMTP server to send through
        $this->mailer->SMTPAuth = true;                               //Enable SMTP authentication
        $this->mailer->Username = EMAIL_USERNAME;                     //SMTP username
        $this->mailer->Password = EMAIL_PASSWORD;                     //SMTP password
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      //Enable implicit TLS encryption
        $this->mailer->Port = EMAIL_PORT;                             //TCP port to connect to
        
        
    }

    /**
     * function used to send a mail with the given infomation
     * 
     * @param string $to Email recipient
     * @param string $subject Email subject
     * @param string $message Email body/message
     * @param string $name displayed name 
     * @param string $attachment attachment that coming with the mail | if there is no attachment $attachment is false 
    */ 
    public function sendMail($to,$subject,$message,$name, $attachment= false){
        try{
            $this->mailer->setFrom(EMAIL, $name);     //the email used to send and the name displayed
            $this->mailer->addAddress($to);     //Add a recipient
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $message;
            if($attachment !== false) $this->mailer->addAttachment($attachment);
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            return $this->mailer->ErrorInfo;
        }

    }
}

?>
