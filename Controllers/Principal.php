<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require 'vendor/autoload.php';

class Principal extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }
    public function index()
    {
        $data['title'] = 'iniciar sesion';
        $this->views->getView('principal', 'index', $data);
    }

    ##login##
    public function validar()
    {
        $correo = $_POST['correo'];
        $clave = $_POST['clave'];
        $data = $this->model->getUsuario($correo);
        if (!empty($data)) {
            if (password_verify($clave, $data['clave'])) {
                setcookie('id', $data['id'], time() + 3600, "/");
                setcookie('correo', $data['correo'], time() + 3600, "/");
                setcookie('nombre', $data['nombre'], time() + 3600, "/");
                $_SESSION['id'] = $data['id'];
                $_SESSION['correo'] = $data['correo'];
                $_SESSION['nombre'] = $data['nombre'];
                $res = array('tipo' => 'success', 'mensaje' => 'BIENVENIDO AL SISTEMA DE GESTOR DE ARCHIVOS');
            } else {
                $res = array('tipo' => 'warning', 'mensaje' => 'LA CONTRASEÑA INCORRECTA');
            }
        } else {
            $res = array('tipo' => 'warning', 'mensaje' => 'EL CORREO NO EXISTE');
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    ##cambiar clave

    public function enviarCorreo($correo)
    {

        $consulta = $this->model->getUsuario($correo);
        if (!empty($consulta)) {
            $mail = new PHPMailer(true);
        try {
            $token = md5(date('YmdHis'));
            $this->model->updateToken($token, $correo);
            //Server settings
            $mail->SMTPDebug = 0;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'katomogollon@gmail.com';                     //SMTP username
            $mail->Password   = 'zgoxororohqpqgdx';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('katomogollon@gmail.com', 'marcelo alburqueque');
            $mail->addAddress($correo); 

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Restablecer Clave';
            $mail->Body    = 'Has pedido restablcer tu contraseña, si no has sido tu omite este mensaje <br><a href="'. BASE_URL .'principal/reset/'.$token.'">CLICK AQUI PARA CAMBIAR CONTRASEÑA</a>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        }else{
            $res = array('tipo' => 'warning', 'mensaje' => 'EL CORREO NO EXISTE');
        }
    }

    public function reset($token)
    {
        $data['title'] = 'Reestablecer clave';
        $data['usuarios'] = $this->model->getToken($token);
        if ($data['usuarios']['token'] == $token) {
           $this->views->getView('principal', 'reset', $data);
        }else{
            header('Location: ' .  BASE_URL . 'errorPag');
        }
        
    }
}
