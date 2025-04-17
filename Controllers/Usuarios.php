<?php
class Usuarios extends Controller
{
    public function __construct() {
        parent::__construct();
        session_start();
    }
    public function index()
    {
        $data['title'] = 'Gestion de Usuarios';
        $data['script'] = 'usuarios.js';
        $this->views->getView('usuarios', 'index', $data);
    }
    public Function guardar() 
    {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $clave = $_POST['clave'];
        $rol = $_POST['rol'];
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        $data = $this->model->guardar($nombre, $apellido, $correo, $telefono, $direccion, $hash, $rol);
        if ($data > 0) {
            $res = array('tipo' => 'success', 'mensaje' => 'USUARIO REGISTRADO');
        }else{
            $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL REGISTRAR');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

}