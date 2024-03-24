<?php

namespace Classes;

// Librería de PHP para el envío de email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Clase que representa los correos electrónicos utilizados por la aplicación
 */
class Email
{
    /**
     * Cadena con la dirección de email del destinatario
     *
     * @var string
     */
    protected $email;
    /**
     * Cadena con el nombre del remitente del email
     *
     * @var string
     */
    protected $name;
    /**
     * Cadena con el apellido/s del remitente del email
     *
     * @var string
     */
    protected $last_name;
    /**
     * Cadena que contiene un token para su comprobación
     * en el cambio de la contraseña de la cuenta
     *
     * @var string
     */
    protected $token;

    /**
     * Constructor de la clase
     *
     * @param string $email
     * @param string $name
     * @param string $last_name
     * @param string $token
     */
    public function __construct($email, $name, $last_name, $token)
    {
        $this->email = $email;
        $this->name = $name;
        $this->last_name = $last_name;
        $this->token = $token;
    }

    /**
     * Función que envía un email de confirmación de cuenta 
     * a los usuarios registrados
     *
     * @return void
     */
    public function enviarConfirmacion()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@nexttask.com');
        $mail->addAddress('cuentas@nexttask.com', 'nexttask.com');
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        // Contenido del email
        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->name . " " . $this->last_name . "</strong>. Has creado una cuenta en NextTask,
        pulsa el siguiente enlace para confirmarla.</p>";
        $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['PROJECT_URL'] . "/confirm?token=" . $this->token . "'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        // Enviar el email
        $mail->send();
    }

    /**
     * Función que envía un email con un enlace al usuario para
     * recuperar su contraseña
     *
     * @return void
     */
    public function enviarInstrucciones()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@nexttask.com');
        $mail->addAddress('cuentas@nexttask.com', 'nexttask.com');
        $mail->Subject = 'Reestablece tu contraseña';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        // Contenido del email
        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->name . " " . $this->last_name . "</strong>. Parece que has olvidado tu contraseña, 
        sigue el siguiente enlace para reestablecerla.</p>";
        $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['PROJECT_URL'] . "/reset?token=" . $this->token . "'>Reestablecer contraseña</a></p>";
        $contenido .= "<p>Si no solicitaste el cambio de contraseña, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        // Enviar el email
        $mail->send();

    }

    /**
     * Función que envía una invitación por email a un usuario
     * para que colabore en un proyecto
     *
     * @return void
     */
    public function enviarInvitacion()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@nexttask.com');
        $mail->addAddress('cuentas@nexttask.com', 'nexttask.com');
        $mail->Subject = 'Has sido invitado a colaborar en un proyecto';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        // Contenido del email
        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->email . "</strong>. Has sido invitado por " . $this->name . " " . $this->last_name . " a colaborar en un proyecto en NextTask.</p>";
        $contenido .= "<p>Regístrate aquí para colaborar: <a href='". $_ENV['PROJECT_URL'] ."'>Registrar</a></p>";
        $contenido .= "<p>Saludos, el equipo de NextTask</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Activa el modo de depuración de PHPMailer

        // Enviar el email
        return $mail->send();

    }
}
