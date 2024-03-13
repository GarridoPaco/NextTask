<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    protected $email;
    protected $name;
    protected $last_name;
    protected $token;

    public function __construct($email, $name, $last_name, $token)
    {
        $this->email = $email;
        $this->name = $name;
        $this->last_name = $last_name;
        $this->token = $token;
    }

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

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->email . "</strong>. Has sido invitado por " . $this->name . " " . $this->last_name . " a colaborar en un proyecto en NextTask.</p>";
        $contenido .= "<p>Regístrate aquí para colaborar: <a href='". $_ENV['PROJECT_URL'] ."'>Registrar</a></p>";
        $contenido .= "<p>Saludos, el equipo de NextTask</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        // Enviar el email
        return $mail->send();

    }
}
