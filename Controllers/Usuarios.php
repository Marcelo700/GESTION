<?php
class Usuarios extends Controller
{
    private $id_usuario, $correo;
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->id_usuario = $_COOKIE['id'];
        $this->correo = $_COOKIE['correo'];
    }
    public function index()
    {
        $data['title'] = 'Gestion de Usuarios';
        $data['script'] = 'usuarios.js';
        $data['menu'] = 'usuarios';
        $data['shares'] = $this->model->verificarEstado($this->correo); 
        $this->views->getView('usuarios', 'index', $data);
    }

    public function listar()
    {
        $data = $this->model->getUsuarios();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['id'] == 1) {
                $data[$i]['acciones'] = 'SUPER ADMIN';
            } else {
                $data[$i]['acciones'] = '<div>
                <a href="#" class="btn btn-info btn-sm" onclick="editar(' . $data[$i]['id'] . ') ">
                    editar
                </a>
                <a href="#" class="btn btn-danger btn-sm" onclick="eliminar(' . $data[$i]['id'] . ')">
                    Eliminar
                </a>
            </div>';
            }
            $data[$i]['nombres'] = $data[$i]['nombre'] . ' ' . $data[$i]['apellido'];
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
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
        $id_usuario = $_POST['id_usuario'];
        if (
            empty($nombre) || empty($apellido) || empty($correo) || empty($telefono) 
            || empty($direccion) || empty($clave) || empty($rol)
        ) {
            $res = array('tipo' => 'Warning', 'mensaje' => 'TODOS LOS CAMPOS SON REQUERIDOS');
        } else {
            if ($id_usuario == '') {
                ## comprobar si exite datos
                $verificarCorreo = $this->model->getVerificar('correo', $correo, 0);
                if (empty($verificarCorreo)) {
                    # comprobar si existe telefono
                    $verificarTel = $this->model->getVerificar('telefono', $telefono, 0);
                    if (empty($verificarTel)) {
                        $hash = password_hash($clave, PASSWORD_DEFAULT);
                        $data = $this->model->registrar($nombre, $apellido, $correo, $telefono, $direccion, $hash, $rol);
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
            } else {
                ## comprobar si exite datos
                $verificarCorreo = $this->model->getVerificar('correo', $correo, $id_usuario);
                if (empty($verificarCorreo)) {
                    # comprobar si existe telefono
                    $verificarTel = $this->model->getVerificar('telefono', $telefono, $id_usuario);
                    if (empty($verificarTel)) {
                        $hash = password_hash($clave, PASSWORD_DEFAULT);
                        $data = $this->model->modificar($nombre, $apellido, $correo, $telefono, $direccion, $rol, $id_usuario);
                        if ($data == 1) {
                            $res = array('tipo' => 'success', 'mensaje' => 'USUARIO MODIFICADO');
                        } else {
                            $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL MODIFICAR');
                        }
                    } else {
                        $res = array('tipo' => 'error', 'mensaje' => 'EL TELEFONO YA EXISTE');
                    }
                } else {
                    $res = array('tipo' => 'error', 'mensaje' => 'EL CORREO YA EXISTE');
                }
            }
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function delete($id)
    {
        $data = $this->model->delete($id);
        if ($data == 1) {
            $res = array('tipo' => 'success', 'mensaje' => 'USUARIO DADO DE BAJA');
        } else {
            $res = array('tipo' => 'Warning', 'mensaje' => 'ERROR AL ELIMINARSE');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function editar($id)
    {
        $data = $this->model->getUsuario($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
}
