<?php
// Incluir la libreria PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Wrapper sobre la clase PHPMailer para facilitar envío de correo
 * para recuperación de contraseña.
 */
class Email{

    /**
     * Función que envía un mail a destino indicado, desde cuenta configurada proyecto (hardcodeada)
     * @param string $destino Mail del destinatario
     * @param string $asunto Asunto del mail
     * @param string $mensaje Cuerpo del mensaje HTML
     * @return bool true si se envía correctamente, false en caso contrario.
     */
    public static function enviar($destino,$asunto,$mensaje):bool{
        // Mostrar errores PHP (Desactivar en producción)
        //ini_set('display_errors', 1);
        //ini_set('display_startup_errors', 1);
        //error_reporting(E_ALL);

        //Requisitos PHP Mailer
        require_once('../app/lib/PHPMailer/src/Exception.php');
        require_once('../app/lib/PHPMailer/src/PHPMailer.php');
        require_once('../app/lib/PHPMailer/src/SMTP.php');

        $mail = new PHPMailer(true);                                //Create an instance; passing `true` enables exceptions
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                //Enable verbose debug output
            $mail->isSMTP();                                        //Send using SMTP
            $mail->Host       = 'segundapantalla.es';               //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                               //Enable SMTP authentication
            $mail->Username   = 'proyecto_gpc@segundapantalla.es';  //SMTP username
            $mail->Password   = 'Segunda.123.Mail';                 //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;        //Enable implicit TLS encryption
            $mail->Port       = 465;                                //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            // Solo sin SSL: $mail->SMTPSecure = false; $mail->SMTPAutoTLS = false;

            //Recipients
            $mail->setFrom('proyecto_gpc@segundapantalla.es', 'Gerardo');
            // $mail->addAddress('proyecto_gpc@segundapantalla.es', 'Gerar P.C.');     //Add a recipient
            $mail->addAddress('pcgerard@gmail.com', 'Gerar P.C.');  //Add a recipient

            //Posibles parámetros adicionales...
            //$mail->addAddress('ellen@example.com');               //Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');
            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->CharSet='UTF-8';             //Encoding
            $mail->isHTML(true);                //Set email format to HTML
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;
            $mail->AltBody = $mensaje;          //cuerpo si no se envía en HTML.

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }


}