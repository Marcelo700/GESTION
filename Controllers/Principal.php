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

                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'katomogollon@gmail.com';
                $mail->Password   = 'zgoxororohqpqgdx';  // Mejor usar variable de entorno
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                $mail->CharSet    = 'UTF-8';

                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ];

                $mail->setFrom('katomogollon@gmail.com', 'Marcelo Alburqueque');
                $mail->addAddress($correo);

                $mail->isHTML(true);
                $mail->Subject = 'Restablecer Clave';

                // Cuerpo HTML mejorado
                $mail->Body = '
            <html>
            <head>
              <style>
                .container {
                  font-family: Arial, sans-serif;
                  max-width: 600px;
                  margin: 0 auto;
                  padding: 20px;
                  background-color: #f5f7fa;
                  border-radius: 8px;
                  color: #333333;
                }
                .header {
                  font-size: 24px;
                  font-weight: bold;
                  color: #0d6efd;
                  margin-bottom: 10px;
                }
                .content {
                  font-size: 16px;
                  line-height: 1.5;
                  margin-bottom: 20px;
                }
                .button {
                  display: inline-block;
                  padding: 12px 25px;
                  font-size: 16px;
                  color: white !important;
                  background-color: #0d6efd;
                  text-decoration: none;
                  border-radius: 5px;
                  font-weight: bold;
                }
                .footer {
                  margin-top: 30px;
                  font-size: 12px;
                  color: #888888;
                }
              </style>
            </head>
            <body>
              <div class="container">
                <div class="header">Restablecer tu contraseña</div>
                <div class="content">
                  Hola,<br><br>
                  Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.<br>
                  Si no has realizado esta solicitud, puedes ignorar este correo con seguridad.<br><br>
                  Para cambiar tu contraseña, haz clic en el botón de abajo:
                </div>
                <a href="' . BASE_URL . 'principal/reset/' . $token . '" class="button" target="_blank">Cambiar contraseña</a>
              </div>
            </body>
            </html>
            ';

                // Versión texto plano
                $mail->AltBody = "Hola,\n\n" .
                    "Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.\n" .
                    "Si no has realizado esta solicitud, puedes ignorar este correo con seguridad.\n\n" .
                    "Para cambiar tu contraseña, visita el siguiente enlace:\n" .
                    BASE_URL . "principal/reset/" . $token . "\n\n" .
                    "Si no solicitaste este correo, no es necesario que hagas nada.";

                $mail->send();
                $res = array('tipo' => 'success', 'mensaje' => 'CORREO ENVIADO CON UN TOKEN DE SEGURIDAD');
            } catch (Exception $e) {
                // Manejo de errores en el envío del correo
                $res = array('tipo' => 'error', 'mensaje' => 'No se pudo enviar el correo. Error: ' . $mail->ErrorInfo);
            }
        } else {
            // Si el correo no existe en la base de datos
            $res = array('tipo' => 'warning', 'mensaje' => 'El correo no existe en la base de datos.');
        }

        // Responder con la respuesta JSON
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }


    public function reset($token)
    {
        $data['title'] = 'Reestablecer clave';
        $data['usuarios'] = $this->model->getToken($token);
        if ($data['usuarios']['token'] == $token) {
            $this->views->getView('principal', 'reset', $data);
        } else {
            header('Location: ' .  BASE_URL . 'errorPag');
        }
    }

    public function cambiarPass()
    {
        $nueva = $_POST['clave_nueva'];
        $confirmar = $_POST['clave_confirmar'];
        $token = $_POST['token'];
        if (empty($nueva) || empty($confirmar) || empty($token)) {
            $res = array('tipo' => 'Warning', 'mensaje' => 'TODOS LOS CAMPOS SON REQUERIDOS');
        } else {
            if ($nueva != $confirmar) {
                $res = array('tipo' => 'Warning', 'mensaje' => 'LAS CONTRASEÑAS NO COINCIDEN');
            } else {
                $result = $this->model->getToken($token);
                if ($token == $result['token']) {
                    $hash = password_hash($nueva, PASSWORD_DEFAULT);
                    $data = $this->model->cambiarPass($hash, $token);
                    if ($data == 1) {
                        $res = array('tipo' => 'success', 'mensaje' => 'CONTRASEÑA RESTABLECIDA');
                    } else {
                        $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL RESTABLECER LA CONTRASEÑA');
                    }
                }
            }
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }
}
