<?php

namespace App\Library\Email;

use App\Model\ConfigEmail;
use App\Model\InfoEmpresa;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    protected $email;
    protected $nombre;
    protected $message;
    protected $rutaArchivo;
    protected $nombreArchivo;

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

    public function sendXml($rutaxml, $nombrexml)
    {
        $this->rutaArchivo = $rutaxml;
        $this->nombreArchivo = $nombrexml;

        //diseñar un mensaje con estilos css y del envio de xml
        $contenido = '<html>';
        $contenido .= '<head>';
        $contenido .= '<title>Envio de XML</title>';
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
        $contenido .= '<h1 class="title">Comprobante de venta formato XML</h1>';
        $contenido .= '<p class="saludo">Hola: <span>' . $this->nombre . '</span></p>';
        $contenido .= '<p class="mensajea">Se envio el Archivo XML de su comprobante de Venta</p>';
        $contenido .= '<p class="mensajeb">Vuelva pronto a nuestra tienda</p>';
        $contenido .= '</div>';
        $contenido .= '</body>';
        $contenido .= '</html>';

        $this->message = $contenido;
    }

    public function sendCdr($rutaxml, $nombrexml)
    {
        $this->rutaArchivo = $rutaxml;
        $this->nombreArchivo = $nombrexml;

        //diseñar un mensaje con estilos css y del envio de xml
        $contenido = '<html>';
        $contenido .= '<head>';
        $contenido .= '<title>Envio de XML</title>';
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
        $contenido .= '<h1 class="title">CDR (constancia de recepción) de SUNAT</h1>';
        $contenido .= '<p class="saludo">Hola: <span>' . $this->nombre . '</span></p>';
        $contenido .= '<p class="mensajea">Este es el Archivo de la constancia de recepción emitido por la sunat</p>';
        $contenido .= '<p class="mensajeb">Vuelva pronto a nuestra tienda</p>';
        $contenido .= '</div>';
        $contenido .= '</body>';
        $contenido .= '</html>';

        $this->message = $contenido;
    }

    public function send()
    {
        $id = 1;
        $data  = ConfigEmail::find($id);
        $empresa = InfoEmpresa::find($id);

        //crear una isntancia de PHPmailer
        $mail = new PHPMailer();
        //CONFIGURADOR DEL SERVIDOR
        $mail->isSMTP();
        $mail->Host = $data->servidor; //servidor de correo
        $mail->SMTPAuth = true; // habilitar autenticacion
        $mail->Username = $data->correo_servidor; // usuario del correo
        $mail->Password = $data->contrasena_servidor; // contraseña del correo
        // $mail->SMTPSecure = "tls";
        $mail->SMTPSecure = $data->tipo_protocolo; // habilitar encriptacion
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port  = $data->puerto; // puerto de salida

        //CONFIGURADOR DEL CORREO
        $mail->setFrom($empresa->email, $empresa->nombre_comercial); // correo y nombre del remitente
        $mail->addAddress($this->email, $this->nombre); // correo y nombre del destinatario
        $mail->Subject = "Reestablecer contraseña"; // asunto del correo
        $mail->isHTML(true); // habilitar el contenido html
        $mail->CharSet = "UTF-8"; // codificacion del correo

        //CONTENIDO DEL CORREO
        $mail->Body = $this->message;

        //enviar archivo adjunto
        if ($this->rutaArchivo != null) {
            $mail->addAttachment($this->rutaArchivo, $this->nombreArchivo);
        }

        $result = $mail->send();
        return $result;
    }
}
