<?php
class Admin extends Controller
{
    private $id_usuario, $correo;
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (isset($_COOKIE['id'])) {
            $this->id_usuario = $_COOKIE['id'];
            $this->correo = $_COOKIE['correo'];
            $_SESSION["nombre"] = $_COOKIE['nombre'];
            $_SESSION["correo"] = $_COOKIE['correo'];
            ## validar sesion
            if (empty($_SESSION['id'])){
                header('location: ' . BASE_URL);
                exit;
            }
            ## eliminar archivos de forma permanente
            $fecha = date('Y-m-d H:i:s');
            $eliminar = $this->model->getConsulta();
            $ruta = 'Assets/archivos/';
            for ($i = 0; $i < count($eliminar); $i++) {
                if ($eliminar[$i]['elimina'] < $fecha) {
                    $accion = $this->model->eliminarRegistro($eliminar[$i]['id']);
                    if ($accion == 1) {
                        if (file_exists($ruta . $eliminar[$i]['id_carpeta'] . '/' . $eliminar[$i]['nombre'])) {
                            unlink($ruta . $eliminar[$i]['id_carpeta'] . '/' . $eliminar[$i]['nombre']);
                        }
                    }
                }
            }
        } else {
            header("Location: http://localhost:81/gestion/");
        }
    }
    public function index()
    {
        $data['title'] = 'Panel de administracion';
        $data['script'] = 'file.js';
        $data['active'] = 'recent';
        $data['menu'] = 'admin';
        $carpetas = $this->model->getCarpetas($this->id_usuario);
        $data['archivos'] = $this->model->getArchivosRecientes($this->id_usuario);
        for ($i = 0; $i < count($carpetas); $i++) {
            $carpetas[$i]['color'] = substr(md5($carpetas[$i]['id']), 0, 6);
            $carpetas[$i]['fecha'] = time_ago(strtotime($carpetas[$i]['fecha_create']));
        }
        $data['carpetas'] = $carpetas;
        $data['shares'] = $this->model->verificarEstado($this->correo);
        $this->views->getView('admin', 'home', $data);
    }

    public function crearcarpeta()
    {
        $nombre = $_POST['nombre'];
        if (empty($nombre)) {
            $res = array('tipo' => 'Warning', 'mensaje' => 'EL NOMBRE ES REQUERIDOS');
        } else {
            # COMPROBAR NOMBRE
            $verificarNom = $this->model->getVerificar('nombre', $nombre, $this->id_usuario, 0);
            if (empty($verificarNom)) {
                $data = $this->model->crearcarpeta($nombre, $this->id_usuario);
                if ($data > 0) {
                    $res = array('tipo' => 'success', 'mensaje' => 'CARPETA CREADA');
                } else {
                    $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL CREAR LA CARPETA');
                }
            } else {
                $res = array('tipo' => 'error', 'mensaje' => 'LA CARPETA YA EXISTE');
            }
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function subirArchivos()
    {
        $id_carpeta = (empty($_POST['id_carpeta'])) ? 1 : $_POST['id_carpeta'];
        $archivo = $_FILES['file'];
        $name = $archivo['name'];
        $tmp = $archivo['tmp_name'];
        $tipo = $archivo['type'];
        $data = $this->model->subirArchivos($name, $tipo, $id_carpeta, $this->id_usuario);
        if ($data > 0) {
            $destino = 'Assets/archivos';
            if (!file_exists($destino)) {
                mkdir($destino);
            }
            $carpeta = $destino . '/' . $id_carpeta;
            if (!file_exists($carpeta)) {
                mkdir($carpeta);
            }
            move_uploaded_file($tmp, $carpeta . '/' . $name);
            $id_archivo = $data; // ID del archivo insertado

            // --- INTEGRACIÓN OPENAI ---
            require_once __DIR__ . '/../openai_helper.php';
            $ruta_guardado = $carpeta . '/' . $name;
            list($etiquetas, $carpetaSugerida) = obtenerEtiquetasYCarpetaOpenAI($ruta_guardado, $name);

            // 1. Crear carpeta sugerida si no existe
            $id_carpeta_sugerida = $id_carpeta;
            if ($carpetaSugerida && strtolower($carpetaSugerida) !== strtolower($this->model->getCarpeta($id_carpeta)['nombre'])) {
                // Buscar si existe carpeta con ese nombre
                $adminModel = $this->model;
                $carpeta_existente = $adminModel->getVerificar('nombre', $carpetaSugerida, $this->id_usuario, 0);
                if (empty($carpeta_existente)) {
                    $id_carpeta_sugerida = $adminModel->crearcarpeta($carpetaSugerida, $this->id_usuario);
                } else {
                    $carpeta_info = $adminModel->getVerificar('nombre', $carpetaSugerida, $this->id_usuario, 0);
                    $id_carpeta_sugerida = $carpeta_info['id'];
                }
            }

            // 2. Mover archivo si la carpeta sugerida es diferente
            if ($id_carpeta != $id_carpeta_sugerida) {
                $nuevo_destino = $destino . '/' . $id_carpeta_sugerida;
                if (!file_exists($nuevo_destino)) {
                    mkdir($nuevo_destino);
                }
                rename($ruta_guardado, $nuevo_destino . '/' . $name);
                // Actualizar en la base de datos el id_carpeta del archivo
                $this->model->actualizarCarpetaArchivo($id_archivo, $id_carpeta_sugerida);
            }

            // 3. Crear etiquetas si no existen y vincularlas a la carpeta y al archivo
            require_once __DIR__ . '/../Models/EtiquetasModel.php';
            $etiquetasModel = new EtiquetasModel();
            foreach ($etiquetas as $etiqueta) {
                // Buscar o crear etiqueta
                $etiqueta_existente = $etiquetasModel->getEtiquetaPorNombre($etiqueta);
                if (empty($etiqueta_existente)) {
                    $id_etiqueta = $etiquetasModel->registrar($etiqueta, '#563d7c'); // color por defecto
                } else {
                    $id_etiqueta = $etiqueta_existente['id'];
                }
                // Vincular etiqueta a carpeta y archivo
                $etiquetasModel->asignarEtiqueta($id_etiqueta, null, $id_carpeta_sugerida);
                $etiquetasModel->asignarEtiqueta($id_etiqueta, $id_archivo, null);
            }

            $res = array('tipo' => 'success', 'mensaje' => 'ARCHIVO SUBIDO Y CLASIFICADO CON OPENAI');
        } else {
            $res = array('tipo' => 'error', 'mensaje' => 'ERROR AL SUBIR EL ARCHIVO');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function ver($id_carpeta)
    {
        $data['title'] = 'Listado de archivos';
        $data['active'] = 'detail';
        $data['archivos'] = $this->model->getArchivos($id_carpeta, $this->id_usuario);
        $data['menu'] = 'admin';
        $data['carpeta'] = $this->model->getCarpeta($id_carpeta);
        $data['shares'] = $this->model->verificarEstado($this->correo);
        $this->views->getView('admin', 'archivos', $data);
    }

    public function verdetalle($id_carpeta)
    {
        $data['title'] = 'Archivos compartidos';
        $data['id_carpeta'] = $id_carpeta;
        $data['script'] = 'detail.js';
        $data['carpeta'] = $this->model->getCarpeta($id_carpeta);
        if (empty($data['carpeta'])) {
            echo 'PAGINA NO ENCONTRADA';
            exit;
        }
        $data['menu'] = 'admin';
        $data['shares'] = $this->model->verificarEstado($this->correo);
        $this->views->getView('admin', 'detalle', $data);
    }

    public function listardetalle($id_carpeta)
    {
        $data = $this->model->getArchivosCompartidos($id_carpeta);
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['estado'] == 0) {
                $data[$i]['estado'] = '<span class="badge bg-warning">Se elimina ' . $data[$i]['elimina'] . '</span>';
                $data[$i]['acciones'] = '';
            } else {
                $data[$i]['estado'] = '<span class="badge bg-success">Compartido</span>';
                $data[$i]['acciones'] = '<button class="btn btn-danger btn-sm" onclick="eliminarDetalle(' . $data[$i]['id'] . ')">Eliminar</button>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
}
