<?php
class Usuarios extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }
    public function index()
    {
        $data['title'] = 'Gestion de Usuarios';
        $data['script'] = 'usuarios.js';
        $this->views->getView('usuarios', 'index', $data);
    }
    public function guardar()
    {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $clave = $_POST['clave'];
        $rol = $_POST['rol'];
        if (
            empty($nombre) || empty($apellido) || empty($correo) || empty($telefono) ||
            empty($direccion) || empty($clave) || empty($rol)
        ) {
            $res = array('tipo' => 'Warning', 'mensaje' => 'TODOS LOS CAMPOS SON REQUERIDOS SERVIDOR');
        } else {
            ## comprobar si exite datos
            $verificarCorreo = $this->model->getVerificar('correo', $correo);
            if (empty($verificarCorreo)) {
                # comprobar si existe telefono
                $verificarTel = $this->model->getVerificar('telefono', $telefono);
                if (empty($verificarTel)) {
                    $hash = password_hash($clave, PASSWORD_DEFAULT);
                    $data = $this->model->guardar($nombre, $apellido, $correo, $telefono, $direccion, $hash, $rol);
                    if ($data > 0) {
                        $res = array('tipo' => 'success', 'mensaje' => 'USUARIO REGISTRADO');
                    } else {
                        $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL REGISTRAR');
                    }
                } else {
                    $res = array('tipo' => 'error', 'mensaje' => 'EL TELEFONO YA EXISTE');
                }
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'EL CORREO YA EXISTE');
            }
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }
}
