<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';

class EmailService {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }
    
    private function configureSMTP() {
        $this->mail->isSMTP();
        $this->mail->Host = SMTP_HOST;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = SMTP_USERNAME;
        $this->mail->Password = SMTP_PASSWORD;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = SMTP_PORT;
        $this->mail->setFrom(FROM_EMAIL, FROM_NAME);
        $this->mail->CharSet = 'UTF-8';
    }
    
    public function sendActivationEmail($email, $nombres, $token_action) {
        try {
            $this->mail->addAddress($email, $nombres);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Activa tu cuenta - ' . APP_NAME;
            
            $activationUrl = BASE_URL . "?r=validate/" . $token_action;
            
            $this->mail->Body = "
            <h2>¡Bienvenido a " . APP_NAME . "!</h2>
            <p>Hola {$nombres},</p>
            <p>Gracias por registrarte. Para activar tu cuenta, haz clic en el siguiente botón:</p>
            <p style='text-align: center; margin: 30px 0;'>
                <a href='{$activationUrl}' style='background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; display: inline-block;'>
                    Activar mi cuenta
                </a>
            </p>
            <p>Si no puedes hacer clic en el botón, copia y pega este enlace en tu navegador:</p>
            <p>{$activationUrl}</p>
            <p>Saludos,<br>Equipo de " . APP_NAME . "</p>
            ";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            // Log error to file for debugging
            $errorLog = "Error enviando email: " . $e->getMessage() . "\n";
            $errorLog .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
            $errorLog .= "Email: {$email}\n";
            $errorLog .= "Nombres: {$nombres}\n";
            $errorLog .= "Token: {$token_action}\n\n";
            
            if (!is_dir('emails')) {
                mkdir('emails', 0777, true);
            }
            
            file_put_contents('emails/error_log.txt', $errorLog, FILE_APPEND);
            return false;
        }
    }
}