<?php

namespace App\Library\Email;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    protected $email;
    protected $nombre;
    protected $message;

    public function __construct($email, $name)
    {
        $this->email = $email;
        $this->nombre = $name;
    }

    public function token($token)
    {
        //diseñar un mensaje con estilos css y  con el link de recuperacion de contraseña
        $contenido = '<html>';
        $contenido .= '<head>';
        $contenido .= '<title>Recuperar contraseña</title>';
        $contenido .= '<style>';
        $contenido .= 'body{background-color: #f2f2f2; font-family: Arial, Helvetica, sans-serif;}';
        $contenido .= '.container {width: 100%;max-width: 600px;margin: auto;background-color: #f8f5f5;padding: 0.5rem 2rem;border: #000 1px solid;border-radius: 0.5rem;box-shadow: -10px 0px 13px -7px #000000, 10px 0px 13px -7px #000000, 5px 5px 15px 5px rgba(0, 0, 0, 0);}';
        $contenido .= '.title {color: #060388;padding: 0.5rem;text-align: center;text-decoration: underline;margin: auto;}';
        $contenido .= '.saludo,.mensajea,.mensajeb {font-size: 1.1rem;color: #000;}';
        $contenido .= '.mensajeb {color: #f00;font-size: 0.9rem;text-align: center;}';
        $contenido .= '.saludo span {color: #060388;font-weight: bold;}';
        $contenido .= '.mensajea span {color: #0d0c6e;font-weight: bold;}';
        $contenido .= '.container .enlace {display: block;text-align: center;background-color: #9DBB48;padding: 10px;text-decoration: none; box-shadow: 0px 10px 13px -7px #000000, 5px 5px 15px 5px #00000000;color: #ffffff;}';
        $contenido .= '.container .enlace:hover {background-color: #8b9e3f;}';
        $contenido .= '</style>';
        $contenido .= '</head>';
        $contenido .= '<body>';
        $contenido .= '<div class="container">';
        $contenido .= '<h1 class="title">Cambio de Contraseña</h1>';
        $contenido .= '<p class="saludo">Hola: <span>' . $this->nombre . '</span></p>';
        $contenido .= '<p class="mensajea">Usted solicito un enlace para reestablecer su contraseña de su cuenta <span>' . base_url() . '</span>, haz click en el siguiente enlace:</p>';
        $contenido .= '<a class="enlace" href="' . route('login.reset') . "?token=" . $token . '">Ingresar nueva Contraseña</a>';
        $contenido .= '<p class="mensajeb">Si usted no has solicitado reestabler su contraseña, ignora este mensaje</p>';
        $contenido .= '</div>';
        $contenido .= '</body>';
        $contenido .= '</html>';

        $this->message = $contenido;
    }

    public function send()
    {
        //crear una isntancia de PHPmailer
        $mail = new PHPMailer();
        //CONFIGURADOR DEL SERVIDOR
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com"; //servidor de correo
        $mail->SMTPAuth = true; // habilitar autenticacion
        $mail->Username = "carlitostucno@gmail.com"; // usuario del correo
        $mail->Password = "czfluoplsuelqpsj"; // contraseña del correo
        // $mail->SMTPSecure = "tls";
        // $mail->SMTPSecure = "ssl"; // habilitar encriptacion
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port  = "465"; // puerto de salida

        //CONFIGURADOR DEL CORREO
        $mail->setFrom("admin@admin.com", "Admin"); // correo y nombre del remitente
        $mail->addAddress($this->email, $this->nombre); // correo y nombre del destinatario
        $mail->Subject = "Recuperar contraseña"; // asunto del correo
        $mail->isHTML(true); // habilitar el contenido html
        $mail->CharSet = "UTF-8"; // codificacion del correo

        //CONTENIDO DEL CORREO
        $mail->Body = $this->message;
        $result = $mail->send();
        return $result;
    }
}
